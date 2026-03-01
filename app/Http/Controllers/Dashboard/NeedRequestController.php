<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Project;
use App\Models\Person;
use App\Models\Supervisor;
use App\Models\NeedRequest;
use App\Models\NeedRequestItem;
use App\Models\NeedRequestProject;
use App\Models\NeedRequestSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\NeedRequestRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NeedRequestItemsExport;

class NeedRequestController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * NeedRequestController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(NeedRequest::class, 'need_request');
    }

    public function index()
    {
        NeedRequestProject::checkAndExpire();

        $need_requests = NeedRequest::with(['project.needRequestProject', 'supervisor', 'items'])
            ->filter()
            ->latest()
            ->paginate();

        $notifications = $this->getSkippedItems();

        return view('dashboard.need_requests.index', array_merge(compact('need_requests'), $notifications));
    }

    /**
     * Display supervisor's own requests.
     */
    public function myRequests()
    {
        $this->authorize('viewAnyOwn', NeedRequest::class);

        NeedRequestProject::checkAndExpire();

        $need_requests = NeedRequest::with(['project.needRequestProject', 'items'])
            ->where('supervisor_id', auth()->id())
            ->latest()
            ->paginate();

        $notifications = $this->getSkippedItems();

        return view('dashboard.need_requests.my', array_merge(compact('need_requests'), $notifications));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if need requests are enabled for this supervisor
        if (!NeedRequestSetting::isEnabledFor(auth()->id())) {
            flash()->error(trans('need_requests.messages.not_enabled'));
            return redirect()->route('dashboard.home');
        }

        $notifications = $this->getSkippedItems();

        NeedRequestProject::checkAndExpire();

        // Get enabled projects for need requests
        $projects = Project::active()
            ->with('needRequestProject')
            ->where(function ($query) {
                $query->whereDoesntHave('needRequestProject')
                    ->orWhereHas('needRequestProject', function ($q) {
                        $q->where('is_enabled', true)
                            ->where(function ($sq) {
                                $sq->whereNull('deadline')
                                    ->orWhere('deadline', '>', now());
                            });
                    });
            })
            ->get();

        // Prepare deadlines for JS
        $deadlines = $projects->mapWithKeys(function($project) {
            $deadline = optional($project->needRequestProject)->deadline;
            return [$project->id => $deadline ? $deadline->toIso8601String() : ''];
        });

        // Prepare limits for JS
        $limits = $projects->mapWithKeys(function($project) {
            $limit = optional($project->needRequestProject)->allowed_id_count;
            return [$project->id => $limit ?? ''];
        });

        // Get supervised people (if supervisor has area_responsible relation)
        $people = collect();

        // Get supervisor's area responsible if exists
        $user = auth()->user();
        if ($user->isSupervisor()) {
            $people = Person::where('area_responsible_id', $user->id)
                ->familyHead()
                ->get();
        }

        return view('dashboard.need_requests.create', array_merge(compact('projects', 'people', 'deadlines', 'limits'), $notifications));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NeedRequestRequest $request)
    {
        NeedRequestProject::checkAndExpire();

        // Check if enabled
        if (!NeedRequestSetting::isEnabledFor(auth()->id())) {
            flash()->error(trans('need_requests.messages.not_enabled'));
            return redirect()->route('dashboard.home');
        }

        // Get supervisor's area responsible ID
        $user = auth()->user();

        // Check for existing pending request for this supervisor
        if ($user->isSupervisor()) {
            $hasPending = NeedRequest::where('supervisor_id', $user->id)
                ->where('project_id', $request->project_id)
                ->where('status', 'pending')
                ->exists();
            if ($hasPending) {
                flash()->error('لديك طلب احتياج قيد الانتظار لهذا الكوبون بالفعل. لا يمكنك إضافة طلب جديد لنفس الكوبون حتى يتم معالجة الطلب الحالي.');
                return redirect()->back()->withInput();
            }
        }

        $supervisorAreaId = $user->isSupervisor() ? $user->id : null;

        // Add person IDs
        $personIdsRaw = $request->input('person_ids', []);
        if (is_string($personIdsRaw)) {
            $personIdsRaw = array_filter(array_map('trim', explode("\n", $personIdsRaw)));
        }
        $personIdsRaw = array_unique($personIdsRaw);

        $validIds = [];
        $errors = [
            'notFound' => [],
            'unavailable' => [],
            'processed' => [],
        ];
        $project = Project::with('needRequestProject')->find($request->project_id);

        // Check deadline
        if ($project && $project->needRequestProject && $project->needRequestProject->deadline && $project->needRequestProject->deadline->isPast()) {
            flash()->error("عذراً، انتهى موعد الترشيح لهذا المشروع في " . $project->needRequestProject->deadline->format('Y-m-d H:i'));
            return redirect()->back()->withInput();
        }

        foreach ($personIdsRaw as $idNum) {
            $person = Person::where('id_num', $idNum)->first();

            if (!$person) {
                $errors['notFound'][] = "الهوية {$idNum}: عذراً، هذا الرقم غير مسجل في النظام.";
                continue;
            }

            // 1. Check if already added to this project (as beneficiary)
            if ($project->beneficiaries()->where('person_id', $person->id)->exists()) {
                $errors['processed'][] = "الهوية {$idNum}: هذا الشخص مضاف مسبقاً كمستفيد في هذا المشروع.";
                continue;
            }

            // 2. Check if already requested in the last 30 days
            if (NeedRequestItem::isRequestedInLast30Days($person->id)) {
                $errors['processed'][] = "الهوية {$idNum}: هذا الشخص موجود حالياً في طلب احتياج سابق في اخر 30 يوم";
                continue;
            }

            // 3. Check if approved and has Area Responsible
            if (!$person->area_responsible_id || !$person->block_id) {
                $errors['unavailable'][] = "الهوية {$idNum} ({$person->name}): هذا الشخص غير معتمد أو لا يملك مسؤول منطقة.";
                continue;
            }

            // 4. Check if in supervisor's area
            if (!$user->isAdmin() && $supervisorAreaId && $person->area_responsible_id != $supervisorAreaId) {
                $errors['unavailable'][] = "الهوية {$idNum} ({$person->name}): هذا الشخص خارج نطاق منطقتك المسؤول عنها.";
                continue;
            }

            $validIds[] = $person->id;
        }

        if (empty($validIds)) {
            if (!empty($errors['notFound']) || !empty($errors['unavailable']) || !empty($errors['processed'])) {
                session()->put('need_request_notFound', $errors['notFound']);
                session()->put('need_request_unavailable', $errors['unavailable']);
                session()->put('need_request_processed', $errors['processed']);
                session()->save();
            }
            flash()->error("فشل إرسال الطلب: لم يتم العثور على أي هويات صالحة.");
            return redirect()->back()->withInput();
        }

        // Get allowed id count from project settings
        $projectSettings = NeedRequestProject::where('project_id', $request->project_id)->first();
        $allowedIdCount = $projectSettings ? $projectSettings->allowed_id_count : null;

        // Check allowed id count limit if specified
        if ($allowedIdCount && count($validIds) > $allowedIdCount) {
            flash()->error("عذراً، لا يمكنك إضافة أكثر من {$allowedIdCount} هويات في هذا الطلب.");
            return redirect()->back()->withInput();
        }

        // Create the main request
        $need_request = NeedRequest::create([
            'project_id' => $request->project_id,
            'supervisor_id' => auth()->id(),
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        foreach ($validIds as $personId) {
            NeedRequestItem::create([
                'need_request_id' => $need_request->id,
                'person_id' => $personId,
                'status' => 'pending',
            ]);
        }

        if (!empty($errors['notFound']) || !empty($errors['unavailable']) || !empty($errors['processed'])) {
            session()->put('need_request_notFound', $errors['notFound']);
            session()->put('need_request_unavailable', $errors['unavailable']);
            session()->put('need_request_processed', $errors['processed']);
            session()->save();
        }

        flash()->success(trans('need_requests.messages.created'));

        return redirect()->route('dashboard.need_requests.show', $need_request);
    }

    /**
     * Display the specified resource.
     */
    public function show(NeedRequest $need_request)
    {
        $need_request->load(['project.needRequestProject', 'supervisor', 'items.person', 'reviewedBy']);

        $notifications = $this->getSkippedItems();

        return view('dashboard.need_requests.show', array_merge(compact('need_request'), $notifications));
    }

    /**
     * Get skipped items from session.
     */
    protected function getSkippedItems()
    {
        return [
            'needRequestNotFound' => session()->get('need_request_notFound', []),
            'needRequestUnavailable' => session()->get('need_request_unavailable', []),
            'needRequestProcessed' => session()->get('need_request_processed', []),
        ];
    }

    /**
     * Clear skipped items from session.
     */
    public function clearSession()
    {
        session()->forget([
            'need_request_notFound',
            'need_request_unavailable',
            'need_request_processed',
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NeedRequest $need_request)
    {
        // Only allow editing pending requests
        if (!$need_request->isPending()) {
            flash()->error(trans('need_requests.messages.cannot_edit'));
            return redirect()->route('dashboard.need_requests.show', $need_request);
        }

        // Get project setting for limit display and deadline
        $projectSetting = NeedRequestProject::where('project_id', $need_request->project_id)->first();

        // Check deadline for editing (Strict block)
        if ($projectSetting && $projectSetting->deadline && $projectSetting->deadline->isPast()) {
            flash()->error("عذراً، انتهى موعد الترشيح لهذا المشروع في " . $projectSetting->deadline->format('Y-m-d H:i') . " ولا يمكن تعديل الطلب حالياً.");
            return redirect()->route('dashboard.need_requests.show', $need_request);
        }

        $projects = Project::active()->with('needRequestProject')->get();
        $need_request->load('items.person');

        // Pre-populate person_ids for the textarea
        $need_request->person_ids = $need_request->items->map(function ($item) {
            return $item->person ? $item->person->id_num : null;
        })->filter()->implode("\n");

        // Prepare deadlines and limits for JS
        $deadlines = $projects->mapWithKeys(function($project) {
            $deadline = optional($project->needRequestProject)->deadline;
            return [$project->id => $deadline ? $deadline->toIso8601String() : ''];
        });
        $limits = $projects->mapWithKeys(function($project) {
            $limit = optional($project->needRequestProject)->allowed_id_count;
            return [$project->id => $limit ?? ''];
        });

        return view('dashboard.need_requests.edit', array_merge(compact('need_request', 'projects', 'projectSetting', 'deadlines', 'limits')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NeedRequestRequest $request, NeedRequest $need_request)
    {
        NeedRequestProject::checkAndExpire();

        if (!$need_request->isPending()) {
            flash()->error(trans('need_requests.messages.cannot_edit'));
            return redirect()->route('dashboard.need_requests.show', $need_request);
        }

        $user = auth()->user();
        $supervisorAreaId = $user->isSupervisor() ? $user->id : null;
        $project = Project::with('needRequestProject')->find($request->project_id);

        // Check deadline
        if ($project && $project->needRequestProject && $project->needRequestProject->deadline && $project->needRequestProject->deadline->isPast()) {
            flash()->error("عذراً، انتهى موعد الترشيح لهذا المشروع في " . $project->needRequestProject->deadline->format('Y-m-d H:i'));
            return redirect()->back()->withInput();
        }

        // Process person IDs
        $personIdsRaw = array_filter(array_map('trim', explode("\n", $request->input('person_ids', ''))));
        $personIdsRaw = array_unique($personIdsRaw);

        // Get allowed id count from project settings
        $projectSettings = NeedRequestProject::where('project_id', $request->project_id)->first();
        $limit = $projectSettings ? $projectSettings->allowed_id_count : null;

        if ($limit && count($personIdsRaw) > $limit) {
            flash()->error("عذراً، لا يمكنك تجاوز الحد المسموح ({$limit}) للهويات.");
            return redirect()->back()->withInput();
        }

        $validIds = [];
        $errors = [
            'notFound' => [],
            'unavailable' => [],
            'processed' => [],
        ];

        foreach ($personIdsRaw as $idNum) {
            $person = Person::where('id_num', $idNum)->first();

            if (!$person) {
                $errors['notFound'][] = "الهوية {$idNum}: عذراً، هذا الرقم غير مسجل في النظام.";
                continue;
            }

            // 1. Check if already added to this project (as beneficiary)
            if ($project->beneficiaries()->where('person_id', $person->id)->exists()) {
                $errors['processed'][] = "الهوية {$idNum}: هذا الشخص مضاف مسبقاً كمستفيد في هذا المشروع.";
                continue;
            }

            // 2. Check if already requested in the last 30 days (excluding current request items)
            if (NeedRequestItem::isRequestedInLast30Days($person->id, $need_request->id)) {
                $errors['processed'][] = "الهوية {$idNum}: هذا الشخص موجود حالياً في طلب احتياج سابق في اخر 30 يوم";
                continue;
            }

            // 3. Check if approved and has Area Responsible
            if (!$person->area_responsible_id || !$person->block_id) {
                $errors['unavailable'][] = "الهوية {$idNum} ({$person->name}): هذا الشخص غير معتمد أو لا يملك مسؤول منطقة.";
                continue;
            }

            // 4. Check if in supervisor's area
            if (!$user->isAdmin() && $supervisorAreaId && $person->area_responsible_id != $supervisorAreaId) {
                $errors['unavailable'][] = "الهوية {$idNum} ({$person->name}): هذا الشخص خارج نطاق منطقتك المسؤول عنها.";
                continue;
            }

            $validIds[] = $person->id;
        }

        if (empty($validIds) && !empty($personIdsRaw)) {
            if (!empty($errors['notFound']) || !empty($errors['unavailable']) || !empty($errors['processed'])) {
                session()->put('need_request_notFound', $errors['notFound']);
                session()->put('need_request_unavailable', $errors['unavailable']);
                session()->put('need_request_processed', $errors['processed']);
                session()->save();
            }
            flash()->error("فشل تحديث الطلب: لم يتم العثور على أي هويات صالحة.");
            return redirect()->back()->withInput();
        }

        // Sync items: Delete current and re-add valid ones
        $need_request->items()->delete();
        foreach ($validIds as $personId) {
            NeedRequestItem::create([
                'need_request_id' => $need_request->id,
                'person_id' => $personId,
                'status' => 'pending',
            ]);
        }

        $need_request->update([
            'project_id' => $request->project_id,
            'notes' => $request->notes,
        ]);

        if (!empty($errors['notFound']) || !empty($errors['unavailable']) || !empty($errors['processed'])) {
            session()->put('need_request_notFound', $errors['notFound']);
            session()->put('need_request_unavailable', $errors['unavailable']);
            session()->put('need_request_processed', $errors['processed']);
            session()->save();
        }

        flash()->success(trans('need_requests.messages.updated'));

        return redirect()->route('dashboard.need_requests.show', $need_request);
    }

    /**
     * Show the form for bulk creating need requests (Admin only).
     */
    public function bulkCreate()
    {
        $this->authorize('create', NeedRequest::class);

        $projects = Project::active()->get();
        $supervisors = Supervisor::all();

        return view('dashboard.need_requests.bulk_create', compact('projects', 'supervisors'));
    }

    /**
     * Store bulk created need requests (Admin only).
     */
    public function bulkStore(Request $request)
    {
        $this->authorize('create', NeedRequest::class);

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'supervisor_ids' => 'required|array',
            'supervisor_ids.*' => 'exists:users,id',
            'allowed_id_count' => 'required|integer|min:1',
        ]);

        // Automatically enable the project and supervisors for need requests
        NeedRequestProject::updateOrCreate(
            ['project_id' => $request->project_id],
            [
                'is_enabled' => true,
                'allowed_id_count' => $request->allowed_id_count,
                'deadline' => $request->deadline,
            ]
        );

        $count = 0;
        foreach ($request->supervisor_ids as $supervisorId) {
            NeedRequestSetting::updateOrCreate(
                ['supervisor_id' => $supervisorId],
                ['is_enabled' => true]
            );
            $count++;
        }

        flash()->success("تم تفعيل طلبات الاحتياج لـ {$count} مشرفين بنجاح.");

        return redirect()->route('dashboard.need_requests.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NeedRequest $need_request)
    {
        $need_request->delete();

        flash()->success(trans('need_requests.messages.deleted'));

        return redirect()->route('dashboard.need_requests.index');
    }

    /**
     * Review a need request (approve/reject).
     */
    public function review(Request $request, NeedRequest $need_request)
    {
        $this->authorize('review', $need_request);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $need_request->update([
            'status' => $request->status,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'notes' => $request->notes ?? $need_request->notes,
        ]);

        // If approved, update all items status
        if ($request->status === 'approved') {
            $need_request->items()->update(['status' => 'approved']);
        }

        flash()->success(trans('need_requests.messages.reviewed'));

        return redirect()->route('dashboard.need_requests.show', $need_request);
    }

    /**
     * Export need request items to Excel.
     */
    public function export(NeedRequest $need_request)
    {
        $this->authorize('export', $need_request);

        return Excel::download(new NeedRequestItemsExport($need_request), 'need-request-'.$need_request->id.'.xlsx');
    }

    /**
     * Nominate approved items to project beneficiaries.
     */
    public function nominate(Request $request, NeedRequest $need_request)
    {
        $this->authorize('nominate', $need_request);

        if (!$need_request->isApproved()) {
            flash()->error(trans('need_requests.messages.must_approve_first'));
            return redirect()->route('dashboard.need_requests.show', $need_request);
        }

        $project = $need_request->project;
        $addedCount = 0;

        $itemsQuery = $need_request->items()->approved();

        // If ID numbers are provided, filter items
        if ($request->filled('id_nums')) {
            $idNums = array_filter(array_map('trim', explode("\n", $request->id_nums)));
            if (!empty($idNums)) {
                $itemsQuery->whereHas('person', function ($q) use ($idNums) {
                    $q->whereIn('id_num', $idNums);
                });
            }
        }

        foreach ($itemsQuery->get() as $item) {
            // Check if person is not already a beneficiary
            if (!$project->beneficiaries()->where('person_id', $item->person_id)->exists()) {
                $project->beneficiaries()->attach($item->person_id, [
                    'status' => 'غير مستلم',
                    'notes' => 'تم الإضافة من طلب احتياج #' . $need_request->id,
                ]);
                $addedCount++;
            }
        }

        flash()->success(trans('need_requests.messages.nominated', ['count' => $addedCount]));

        return redirect()->route('dashboard.need_requests.show', $need_request);
    }

    /**
     * Nominate a single item to project beneficiaries.
     */
    public function nominateItem(NeedRequest $need_request, NeedRequestItem $item)
    {
        $this->authorize('nominate', $need_request);

        if (!$need_request->isApproved()) {
            flash()->error(trans('need_requests.messages.must_approve_first'));
            return redirect()->back();
        }

        if ($item->need_request_id !== $need_request->id) {
            abort(404);
        }

        $project = $need_request->project;

        if ($project->beneficiaries()->where('person_id', $item->person_id)->exists()) {
            flash()->warning('هذا الشخص مضاف مسبقاً في المستفيدين.');
            return redirect()->back();
        }

        $project->beneficiaries()->attach($item->person_id, [
            'status' => 'غير مستلم',
            'notes' => 'تم الإضافة من طلب احتياج #' . $need_request->id,
        ]);

        $item->update(['status' => 'approved']);

        flash()->success('تم ترشيح الشخص بنجاح.');

        return redirect()->back();
    }

    // === Settings Methods ===

    /**
     * Display supervisor settings.
     */
    public function settings()
    {
        $this->authorize('manageSettings', NeedRequest::class);

        $supervisors = Supervisor::with('needRequestSetting')->get();

        return view('dashboard.need_requests.settings.supervisors', compact('supervisors'));
    }

    /**
     * Toggle supervisor enabled status.
     */
    public function toggleSupervisor($supervisorId)
    {
        $this->authorize('manageSettings', NeedRequest::class);

        $isEnabled = NeedRequestSetting::toggleFor($supervisorId);

        return response()->json([
            'success' => true,
            'is_enabled' => $isEnabled,
        ]);
    }

    /**
     * Display project settings.
     */
    public function projectSettings()
    {
        $this->authorize('manageSettings', NeedRequest::class);

        $projects = Project::with('needRequestProject')->active()->get();

        return view('dashboard.need_requests.settings.projects', compact('projects'));
    }

    /**
     * Toggle project enabled status.
     */
    public function toggleProject($projectId)
    {
        $this->authorize('manageSettings', NeedRequest::class);

        $isEnabled = NeedRequestProject::toggleFor($projectId);

        return response()->json([
            'success' => true,
            'is_enabled' => $isEnabled,
        ]);
    }

    // === Trashed Methods ===

    public function trashed()
    {
        $this->authorize('viewAnyTrash', NeedRequest::class);

        $need_requests = NeedRequest::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.need_requests.trashed', compact('need_requests'));
    }

    public function showTrashed(NeedRequest $need_request)
    {
        $this->authorize('viewTrash', $need_request);

        return view('dashboard.need_requests.show', compact('need_request'));
    }

    public function restore(NeedRequest $need_request)
    {
        $this->authorize('restore', $need_request);

        $need_request->restore();

        flash()->success(trans('need_requests.messages.restored'));

        return redirect()->route('dashboard.need_requests.trashed');
    }

    public function forceDelete(NeedRequest $need_request)
    {
        $this->authorize('forceDelete', $need_request);

        $need_request->forceDelete();

        flash()->success(trans('need_requests.messages.deleted'));

        return redirect()->route('dashboard.need_requests.trashed');
    }
}
