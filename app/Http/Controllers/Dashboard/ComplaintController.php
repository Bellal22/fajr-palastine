<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Complaint;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ComplaintRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ComplaintController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Complaint::class, 'complaint');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $statusTab = $request->get('tab', 'pending'); // pending, resolved, all
        $typeId = $request->get('complaint_type_id');

        $query = Complaint::filter();

        // 1. تصفية حسب حالة التبويب الرئيسي
        if ($statusTab === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('response')->where(function($q2) {
                    $q2->whereNull('status')->orWhere('status', 'pending');
                });
            });
        } elseif ($statusTab === 'resolved') {
            $query->where(function ($q) {
                $q->whereNotNull('response')->orWhere(function($q2) {
                    $q2->whereNotNull('status')->where('status', '!=', 'pending');
                });
            });
        }

        // 2. تصفية حسب نوع الشكوى إذا تم اختياره
        if ($typeId) {
            $query->where('complaint_type_id', $typeId);
        }

        $complaints = $query->latest()->paginate($request->integer('perPage', 15));

        // حساب العدادات للتبويبات
        $pendingCount = Complaint::where(function ($q) {
            $q->whereNull('response')->where(function($q2) {
                $q2->whereNull('status')->orWhere('status', 'pending');
            });
        })->count();

        $resolvedCount = Complaint::where(function ($q) {
            $q->whereNotNull('response')->orWhere(function($q2) {
                $q2->whereNotNull('status')->where('status', '!=', 'pending');
            });
        })->count();

        $allCount = Complaint::count();

        // أنواع الشكاوى مع عدد الشكاوى المعلقة لكل نوع
        $complaintTypes = \App\Models\ComplaintType::withCount(['complaints' => function ($q) {
            $q->whereNull('response')->where(function($q2) {
                $q2->whereNull('status')->orWhere('status', 'pending');
            });
        }])->get();

        $responseTemplates = \App\Models\ComplaintResponseTemplate::latest()->get();

        return view('dashboard.complaints.index', compact(
            'complaints',
            'statusTab',
            'typeId',
            'pendingCount',
            'resolvedCount',
            'allCount',
            'complaintTypes',
            'responseTemplates'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.complaints.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ComplaintRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ComplaintRequest $request)
    {
        $complaint = Complaint::create($request->all());

        flash()->success(trans('complaints.messages.created'));

        return redirect()->route('dashboard.complaints.show', $complaint);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\Response
     */
    public function show(Complaint $complaint)
    {
        // تحميل العلاقات مع الـ model اللي جاي من الـ Route
        $complaint->load(['person', 'responder']);
        $responseTemplates = \App\Models\ComplaintResponseTemplate::latest()->get();

        return view('dashboard.complaints.show', compact('complaint', 'responseTemplates'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\Response
     */
    public function edit(Complaint $complaint)
    {
        return view('dashboard.complaints.edit', compact('complaint'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ComplaintRequest $request
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ComplaintRequest $request, Complaint $complaint)
    {
        $complaint->update($request->all());

        flash()->success(trans('complaints.messages.updated'));

        return redirect()->route('dashboard.complaints.show', $complaint);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Complaint $complaint
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        flash()->success(trans('complaints.messages.deleted'));

        return redirect()->route('dashboard.complaints.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Complaint::class);

        $complaints = Complaint::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.complaints.trashed', compact('complaints'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Complaint $complaint)
    {
        $this->authorize('viewTrash', $complaint);

        return view('dashboard.complaints.show', compact('complaint'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Complaint $complaint
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Complaint $complaint)
    {
        $this->authorize('restore', $complaint);

        $complaint->restore();

        flash()->success(trans('complaints.messages.restored'));

        return redirect()->route('dashboard.complaints.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Complaint $complaint
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Complaint $complaint)
    {
        $this->authorize('forceDelete', $complaint);

        $complaint->forceDelete();

        flash()->success(trans('complaints.messages.deleted'));

        return redirect()->route('dashboard.complaints.trashed');
    }

    /**
     * Export resolved complaints to Excel.
     */
    public function exportResolved(Request $request)
    {
        $this->authorize('viewAny', Complaint::class);

        $query = Complaint::with(['person', 'complaintType', 'responder'])
            ->where(function ($q) {
                $q->whereNotNull('response')->orWhere(function($q2) {
                    $q2->whereNotNull('status')->where('status', '!=', 'pending');
                });
            });

        if ($request->filled('complaint_type_id')) {
            $query->where('complaint_type_id', $request->complaint_type_id);
        }

        $complaints = $query->latest('responded_at')->latest('updated_at')->get();

        return \Excel::download(new \App\Exports\ResolvedComplaintsExport($complaints), 'تقرير_الشكاوى_المحلولة_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Respond to a single complaint.
     */
    public function respond(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        $request->validate([
            'response' => ['required', 'string'],
            'status' => ['required', 'string', 'in:pending,in_progress,resolved,rejected'],
            'responded_at' => ['nullable', 'date'],
            'response_title' => ['nullable', 'string', 'max:255'],
            'admin_complaint_title' => ['nullable', 'string', 'max:255'],
            'admin_complaint_text' => ['nullable', 'string'],
        ]);

        $respondedAt = $request->filled('responded_at') ? \Carbon\Carbon::parse($request->responded_at) : now();

        $complaint->update([
            'response' => $request->input('response'),
            'status' => $request->input('status'),
            'response_title' => $request->input('response_title'),
            'admin_complaint_title' => $request->input('admin_complaint_title'),
            'admin_complaint_text' => $request->input('admin_complaint_text'),
            'responded_at' => $respondedAt,
            'responded_by' => auth()->id(),
        ]);

        flash()->success('تم حفظ الرد وإغلاق/تحديث حالة الشكوى بنجاح.');

        return redirect()->back();
    }

    /**
     * Bulk respond to multiple selected complaints.
     */
    public function bulkRespond(Request $request)
    {
        $this->authorize('viewAny', Complaint::class);

        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'exists:complaints,id'],
            'response' => ['required', 'string'],
            'status' => ['required', 'string', 'in:pending,in_progress,resolved,rejected'],
            'responded_at' => ['nullable', 'date'],
            'response_title' => ['nullable', 'string', 'max:255'],
        ]);

        $items = $request->input('items', []);
        $respondedAt = $request->filled('responded_at') ? \Carbon\Carbon::parse($request->responded_at) : now();

        $count = Complaint::whereIn('id', $items)->update([
            'response' => $request->input('response'),
            'status' => $request->input('status'),
            'response_title' => $request->input('response_title'),
            'responded_at' => $respondedAt,
            'responded_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        flash()->success("تم الرد بنجاح على ({$count}) من الشكاوى المحددة.");

        return redirect()->back();
    }
}
