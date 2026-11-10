<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AreaResponsible;
use App\Models\Block;
use App\Models\Person;
use App\Models\IncompletePerson;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileAppController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (! $user->isAdmin() && ! $user->hasPermission('manage.admins') && ! $user->hasPermission('manage.mobile_app') && ! $user->hasPermission('manage.outbound_shipments')) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * الصفحة الرئيسية لإدارة الجوال
     */
    public function index()
    {
        $stats = [
            'active_distributors_count' => User::where('type', 'mobile_worker')->where('can_access_mobile_app', true)->count(),
            'assigned_distributors_count' => DB::table('project_beneficiaries')->whereNotNull('user_id')->distinct('user_id')->count(),
            'total_nominations' => DB::table('project_beneficiaries')->count(),
            'total_received' => DB::table('project_beneficiaries')->where('status', 'مستلم')->count(),
            'today_deliveries' => DB::table('project_beneficiaries')
                ->where('status', 'مستلم')
                ->whereDate('updated_at', now()->toDateString())
                ->count(),
        ];

        $topDistributors = DB::table('project_beneficiaries')
            ->join('users', 'users.id', '=', 'project_beneficiaries.user_id')
            ->select('users.name', DB::raw('count(*) as deliveries'))
            ->where('project_beneficiaries.status', 'مستلم')
            ->groupBy('users.id', 'users.name')
            ->orderBy('deliveries', 'desc')
            ->limit(3)
            ->get();

        return view('dashboard.mobile_app.index', compact('stats', 'topDistributors'));
    }

    /**
     * إدارة المستخدمين المسموح لهم بدخول التطبيق
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        } else {
            $query->whereIn('type', ['admin', 'supervisor', 'mobile_worker']);
        }

        $users = $query->orderByRaw("FIELD(type, 'admin', 'mobile_worker', 'supervisor')")
            ->latest()
            ->paginate(20);

        return view('dashboard.mobile_app.users', compact('users'));
    }

    /**
     * صفحة إضافة مستخدم جديد للتطبيق
     */
    public function createUser()
    {
        return view('dashboard.mobile_app.create_user');
    }

    /**
     * تخزين مستخدم جديد للتطبيق
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'type' => 'mobile_worker',
            'can_access_mobile_app' => true,
        ]);

        flash()->success('تم إنشاء مستخدم التطبيق بنجاح');
        return redirect()->route('dashboard.mobile_app.users');
    }

    /**
     * تفعيل/تعطيل وصول المستخدم للتطبيق
     */
    public function toggleUserAccess($id)
    {
        $user = User::findOrFail($id);
        $user->can_access_mobile_app = !$user->can_access_mobile_app;
        $user->save();

        flash()->success('تم تحديث صلاحية الوصول للجوال');
        return back();
    }

    /**
     * إدارة المشاريع المتاحة للتزامن مع الجوال
     */
    public function projects()
    {
        $projects = Project::latest()->paginate(20);
        return view('dashboard.mobile_app.projects', compact('projects'));
    }

    /**
     * صفحة ترشيح الأسماء للمشاريع وتكليف مستخدمي الجوال
     */
    public function nominate(Request $request)
    {
        $projects = Project::latest()->get();
        $users = User::whereIn('type', ['mobile_worker', 'admin'])->get();
        $subWarehouses = \App\Models\SubWarehouse::all();
        
        $selectedProjectId = $request->project_id;
        $selectedUserId = $request->user_id;
        $selectedDate = $request->date;
        $searchIdNums = $request->search_id_nums;

        $currentAssignments = [];
        
        $query = DB::table('project_beneficiaries')
            ->leftJoin('persons', 'persons.id', '=', 'project_beneficiaries.person_id')
            ->leftJoin('incomplete_people', 'incomplete_people.id', '=', 'project_beneficiaries.incomplete_person_id')
            ->leftJoin('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
            ->leftJoin('users', 'users.id', '=', 'project_beneficiaries.user_id')
            ->whereNotNull('project_beneficiaries.user_id')
            ->select(
                'project_beneficiaries.*',
                DB::raw('COALESCE(persons.id_num, incomplete_people.id_num) as id_num'),
                DB::raw('COALESCE(persons.first_name, incomplete_people.first_name) as first_name'),
                DB::raw('COALESCE(persons.father_name, incomplete_people.father_name) as father_name'),
                DB::raw('COALESCE(persons.family_name, incomplete_people.family_name) as family_name'),
                'projects.name as project_name',
                'users.name as user_name'
            );

        $hasFilter = false;

        if ($selectedProjectId) {
            $query->where('project_beneficiaries.project_id', $selectedProjectId);
            $hasFilter = true;
        }

        if ($selectedUserId) {
            $query->where('project_beneficiaries.user_id', $selectedUserId);
            $hasFilter = true;
        }

        if ($selectedDate) {
            $query->whereDate('project_beneficiaries.created_at', $selectedDate);
            $hasFilter = true;
        }

        if (!empty($searchIdNums)) {
            $idNumsRaw = preg_split('/[\s,]+/', $searchIdNums, -1, PREG_SPLIT_NO_EMPTY);
            $paddedIds = array_map(fn($id) => str_pad(trim($id), 9, '0', STR_PAD_LEFT), $idNumsRaw);
            
            $query->where(function($q) use ($paddedIds) {
                $q->whereIn('persons.id_num', $paddedIds)
                  ->orWhereIn('incomplete_people.id_num', $paddedIds);
            });
            $hasFilter = true;
        }

        // إذا لم تكن هناك فلاتر، نعرض تكليفات اليوم كافتراضي
        if (!$hasFilter) {
            $selectedDate = now()->toDateString();
            $query->whereDate('project_beneficiaries.created_at', $selectedDate);
            $hasFilter = true;
        }

        // Only explicitly load if a filter is active to avoid loading all assignments
        if ($hasFilter) {
            $currentAssignments = $query->orderBy('project_beneficiaries.created_at', 'desc')->paginate(500)->withQueryString();
        } else {
            // Need a paginator object even if empty so the view can handle ->total() and ->links() without errors
            $currentAssignments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 500, 1, [
                'path' => request()->url(),
                'query' => request()->query()
            ]);
        }

        return view('dashboard.mobile_app.nominate', compact('projects', 'users', 'subWarehouses', 'selectedProjectId', 'selectedUserId', 'selectedDate', 'searchIdNums', 'currentAssignments'));
    }

    /**
     * تخزين ترشيحات وتكليفات الجوال
     */
    public function storeNominations(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'sub_warehouse_id' => 'required|exists:sub_warehouses,id',
            'id_nums' => 'required|string',
            'conflict_date' => 'nullable|date',
        ]);

        $project = Project::find($request->project_id);
        $conflictingProjectIds = $project->conflicts()->pluck('conflict_id')->toArray();
        $idNumsRaw = preg_split('/[\s,]+/', $request->id_nums, -1, PREG_SPLIT_NO_EMPTY);
        $paddedIds = array_map(fn($id) => str_pad(trim($id), 9, '0', STR_PAD_LEFT), $idNumsRaw);
        $paddedIds = array_unique($paddedIds);

        // 1. جلب كافة الأفراد المُدخَلة دفعة واحدة
        $persons = Person::whereIn('id_num', $paddedIds)->withCount('wives')->get()->keyBy('id_num');
        $incompletePeople = IncompletePerson::whereIn('id_num', $paddedIds)->get()->keyBy('id_num');

        // 2. جلب رؤساء الأسر لكل الأفراد الذين لديهم relative_id (دفعة واحدة بدلاً من recursive query لكل فرد)
        $relativeIds = $persons->whereNotNull('relative_id')->pluck('relative_id')->unique()->filter()->values()->toArray();
        // نجلب جميع رؤساء الأسر المحتملين مرة واحدة
        $allHeads = collect();
        if (!empty($relativeIds)) {
            // نجلب كل الأشخاص الذين id_num يطابق أي relative_id
            // وقد نحتاج تكرار للحالات المتداخلة (رأس عائلة له relative_id آخر)
            $headsLevel1 = Person::whereIn('id_num', $relativeIds)->withCount('wives')->get()->keyBy('id_num');
            $allHeads = $headsLevel1;

            // إذا كان لدى بعض رؤساء المستوى الأول أيضاً relative_id، نجلب المستوى التالي
            $level2Ids = $headsLevel1->whereNotNull('relative_id')->pluck('relative_id')->unique()->filter()->values()->toArray();
            if (!empty($level2Ids)) {
                $headsLevel2 = Person::whereIn('id_num', $level2Ids)->withCount('wives')->get()->keyBy('id_num');
                $allHeads = $allHeads->merge($headsLevel2);

                // المستوى الثالث (احتياطي)
                $level3Ids = $headsLevel2->whereNotNull('relative_id')->pluck('relative_id')->unique()->filter()->values()->toArray();
                if (!empty($level3Ids)) {
                    $headsLevel3 = Person::whereIn('id_num', $level3Ids)->withCount('wives')->get()->keyBy('id_num');
                    $allHeads = $allHeads->merge($headsLevel3);
                }
            }
        }

        // دالة مساعدة: تجد رأس الأسرة النهائي من الـ cache المحلي بدلاً من استعلام DB
        $findHead = function (Person $p) use (&$allHeads, &$findHead): Person {
            if (is_null($p->relative_id)) {
                return $p;
            }
            $paddedRel = str_pad($p->relative_id, 9, '0', STR_PAD_LEFT);
            $head = $allHeads[$paddedRel] ?? null;
            if (!$head) {
                return $p; // لا يوجد رأس عائلة → الشخص نفسه
            }
            return $findHead($head);
        };

        // 3. جلب كافة المستفيدين الحاليين في نفس المشروع لتجنب التكرار
        $existingBeneficiaries = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('status', '!=', 'مستلم')
            ->get();
        
        $existingPersonIds = $existingBeneficiaries->pluck('person_id')->filter()->toArray();
        $existingIncompleteIds = $existingBeneficiaries->pluck('incomplete_person_id')->filter()->toArray();

        // 4. جلب كافة التعارضات في المشروعات الأخرى
        $conflictingPersonIds = [];
        if (!empty($conflictingProjectIds)) {
            $conflictingPersonIds = DB::table('project_beneficiaries')
                ->whereIn('project_id', $conflictingProjectIds)
                ->where('status', '!=', 'مستلم')
                ->pluck('person_id')
                ->filter()
                ->toArray();
        }

        $skipConflict = $request->boolean('skip_conflict');
        $conflictDate = $request->input('conflict_date');

        $allEntities = [];
        $errors = [];
        $mergedCount = 0;
        $imported = 0;

        foreach ($paddedIds as $paddedId) {
            $person = $persons[$paddedId] ?? null;
            $isIncomplete = false;

            if (!$person) {
                $person = $incompletePeople[$paddedId] ?? null;
                $isIncomplete = true;
            }

            if (!$person) {
                $errors[] = "الرقم {$paddedId}: لم يتم العثور عليه في الأفراد أو البيانات غير المكتملة";
                continue;
            }

            // إيجاد رأس الأسرة من الـ cache المحلي (بدون استعلام DB جديد)
            $entity = $isIncomplete ? $person : $findHead($person);

            $entityType = $isIncomplete ? 'incomplete' : 'regular';
            $uniqueKey = $entityType . '_' . $entity->id;

            if (isset($allEntities[$uniqueKey])) {
                $mergedCount++;
                continue;
            }

            // التحقق من التعارض (فقط إذا لم يتم تفعيل خيار التجاوز)
            if (!$skipConflict) {
                if ($entityType == 'regular' && in_array($entity->id, $conflictingPersonIds)) {
                    $errors[] = "الرقم {$paddedId}: موجود مسبقاً في مشروع متعارض (استخدم خيار تجاوز التعارض للإضافة)";
                    continue;
                }

                if ($entityType == 'regular' && in_array($entity->id, $existingPersonIds)) {
                    $errors[] = "الرقم {$paddedId}: موجود حالياً في المشروع ولم يستلم بعد";
                    continue;
                }
                if ($entityType == 'incomplete' && in_array($entity->id, $existingIncompleteIds)) {
                    $errors[] = "الرقم {$paddedId}: موجود حالياً في المشروع (سكراب) ولم يستلم بعد";
                    continue;
                }

                if ($conflictDate) {
                    // فحص إذا كان قد استلم بالفعل بدءاً من هذا التاريخ فصاعداً في أي مشروع
                    $receivedRecord = DB::table('project_beneficiaries')
                        ->join('projects', 'project_beneficiaries.project_id', '=', 'projects.id')
                        ->where($entityType == 'regular' ? 'person_id' : 'incomplete_person_id', $entity->id)
                        ->where('project_beneficiaries.status', 'مستلم')
                        ->where(function($query) use ($conflictDate) {
                            $query->whereDate('project_beneficiaries.delivery_date', '>=', $conflictDate)
                                  ->orWhere(function($q) use ($conflictDate) {
                                      $q->whereNull('project_beneficiaries.delivery_date')
                                        ->whereDate('project_beneficiaries.created_at', '>=', $conflictDate);
                                  });
                        })
                        ->select('projects.name as project_name', 'project_beneficiaries.delivery_date', 'project_beneficiaries.created_at')
                        ->first();

                    if ($receivedRecord) {
                        $receivedDateStr = $receivedRecord->delivery_date ?? \Carbon\Carbon::parse($receivedRecord->created_at)->toDateString();
                        $errors[] = "الرقم {$paddedId}: استلم بالفعل في مشروع ({$receivedRecord->project_name}) بتاريخ {$receivedDateStr} (استخدم خيار تجاوز التعارض للإضافة)";
                        continue;
                    }
                } else {
                    // فحص إذا كان قد استلم بالفعل في نفس المشروع (منع الاستلام المزدوج التلقائي)
                    $alreadyReceived = DB::table('project_beneficiaries')
                        ->where('project_id', $project->id)
                        ->where($entityType == 'regular' ? 'person_id' : 'incomplete_person_id', $entity->id)
                        ->where('status', 'مستلم')
                        ->exists();
                    
                    if ($alreadyReceived) {
                        $errors[] = "الرقم {$paddedId}: استلم بالفعل من هذا المشروع (استخدم خيار تجاوز التعارض لإعادة التكليف)";
                        continue;
                    }
                }
            }

            $quantity = $entityType == 'regular' ? max(1, $entity->wives_count ?? 1) : 1;

            $allEntities[$uniqueKey] = [
                'id' => $entity->id,
                'type' => $entityType,
                'quantity' => $quantity
            ];
        }

        // تنظيف تكليف الموزع: سحب اسم أي شخص استلم فعلياً من قائمة الموزع الحالية ليبقى السجل في المشروع فقط
        // نقوم بذلك هنا لضمان التنظيف حتى لو لم يتم إضافة أسماء جديدة في هذه الرفعة
        DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('user_id', $request->user_id)
            ->where('status', 'مستلم')
            ->update(['user_id' => null]);

        if (empty($allEntities)) {
            if (count($errors) > 0) session()->flash('import_errors', $errors);
            flash()->warning('لم يتم إضافة أي أسماء جديدة. تحقق من الأخطاء.');
            return back();
        }

        DB::beginTransaction();
        try {
            $batchTimestamp = now();
            $insertData = [];
            foreach ($allEntities as $item) {
                $insertData[] = [
                    'project_id' => $project->id,
                    'user_id' => $request->user_id,
                    'sub_warehouse_id' => $request->sub_warehouse_id,
                    'person_id' => $item['type'] == 'regular' ? $item['id'] : null,
                    'incomplete_person_id' => $item['type'] == 'incomplete' ? $item['id'] : null,
                    'quantity' => $item['quantity'],
                    'notes' => $skipConflict ? 'إعادة تكليف (تجاوز تعارض)' : 'تكليف موزع ميداني',
                    'status' => 'غير مستلم',
                    'created_at' => $batchTimestamp,
                    'updated_at' => $batchTimestamp,
                ];
                $imported++;
            }

            // الإدخال المجمّع أسرع بكثير من attach داخل حلقة
            DB::table('project_beneficiaries')->insert($insertData);
            $project->touch();
            DB::commit();

            $msg = "تمت المعالجة بنجاح. ناجح: {$imported}";
            if (count($errors) > 0) $msg .= "، فشل: " . count($errors);
            if ($mergedCount > 0) $msg .= "، دمج عائلات: {$mergedCount}";

            if (count($errors) > 0) {
                flash()->warning($msg);
                session()->flash('import_errors', $errors);
            } else {
                flash()->success($msg);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.mobile_app.nominate', [
            'project_id' => $request->project_id,
            'user_id' => $request->user_id
        ]);
    }

    public function unassign($id)
    {
        DB::table('project_beneficiaries')->where('id', $id)->update(['user_id' => null]);
        flash()->success('تم إلغاء التكليف بنجاح');
        return back();
    }

    /**
     * تنفيذ إجراء جماعي على التكليفات (حذف أو تحويل)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action_type' => 'required|in:delete,transfer',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:project_beneficiaries,id',
            'transfer_project_id' => 'required_if:action_type,transfer',
            'transfer_user_id' => 'required_if:action_type,transfer',
            'transfer_sub_warehouse_id' => 'required_if:action_type,transfer',
        ]);

        $ids = $request->selected_ids;
        $action = $request->action_type;

        // من المهم ضمان أننا نتعامل فقط مع التكليفات التي لم يتم استلامها لحمايتها من التعديل/الحذف الخطأ.
        // التكليفات المنجزة لا يجب نقلها أو حذفها من هذه الشاشة.
        $validIds = DB::table('project_beneficiaries')
            ->whereIn('id', $ids)
            ->where('status', '!=', 'مستلم')
            ->pluck('id')
            ->toArray();

        if (empty($validIds)) {
            flash()->warning('لم يتم تنفيذ الإجراء. يرجى التأكد من اختيار تكليفات غير مستلمة فقط.');
            return back();
        }

        if ($action === 'delete') {
            DB::table('project_beneficiaries')->whereIn('id', $validIds)->delete();
            flash()->success('تم حذف التكليفات المحددة بنجاح.');
        } elseif ($action === 'transfer') {
            DB::table('project_beneficiaries')->whereIn('id', $validIds)->update([
                'project_id' => $request->transfer_project_id,
                'user_id' => $request->transfer_user_id,
                'sub_warehouse_id' => $request->transfer_sub_warehouse_id,
                'updated_at' => now(),
            ]);
            flash()->success('تم تحويل التكليفات المحددة بنجاح للوجهة الجديدة.');
        }

        if (count($ids) > count($validIds)) {
            flash()->info('تنبيه: تم تجاهل بعض السجلات المحددة لأن حالتها "مستلم" ولا يمكن تعديلها.');
        }

        return back();
    }

    /**
     * تصدير كشف الترشيحات لموظف محدد كملف Excel
     */
    public function exportNominations(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $project = Project::find($request->project_id);
        $user = User::find($request->user_id);

        $fileName = "ترشيحات_{$user->name}_{$project->name}_" . now()->format('Y-m-d') . ".xlsx";
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MobileWorkerNominationsExport($request->project_id, $request->user_id), 
            $fileName
        );
    }

    /**
     * تقرير شامل لترشيحات المشروع والموزعين
     */
    public function projectReport(Request $request)
    {
        $projects = Project::latest()->get();
        $selectedProjectId = $request->project_id;

        $groupedNominations = collect();
        if ($selectedProjectId) {
            $rawNominations = DB::table('project_beneficiaries')
                ->leftJoin('persons', 'persons.id', '=', 'project_beneficiaries.person_id')
                ->leftJoin('incomplete_people', 'incomplete_people.id', '=', 'project_beneficiaries.incomplete_person_id')
                ->leftJoin('users', 'users.id', '=', 'project_beneficiaries.user_id')
                ->leftJoin('sub_warehouses', 'sub_warehouses.id', '=', 'project_beneficiaries.sub_warehouse_id')
                ->where('project_beneficiaries.project_id', $selectedProjectId)
                ->whereNotNull('project_beneficiaries.user_id')
                ->select(
                    'project_beneficiaries.*',
                    DB::raw('COALESCE(project_beneficiaries.delivery_date, project_beneficiaries.created_at) as batch_date'),
                    DB::raw('COALESCE(persons.id_num, incomplete_people.id_num) as id_num'),
                    DB::raw('COALESCE(persons.first_name, incomplete_people.first_name) as first_name'),
                    DB::raw('COALESCE(persons.father_name, incomplete_people.father_name) as father_name'),
                    DB::raw('COALESCE(persons.family_name, incomplete_people.family_name) as family_name'),
                    'users.name as worker_name',
                    'sub_warehouses.name as warehouse_name'
                )
                ->orderBy('project_beneficiaries.created_at', 'desc')
                ->orderBy('project_beneficiaries.id', 'desc')
                ->get();

            // Group by User, Warehouse and specific Minute (to separate distinct batches while keeping records together)
            $groupedNominations = $rawNominations->groupBy(function($item) {
                $timePart = \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i');
                return $item->user_id . '_' . $item->sub_warehouse_id . '_' . $timePart;
            })->sortByDesc(function($group) {
                return $group->first()->id;
            });
        }

        return view('dashboard.mobile_app.report', compact('projects', 'selectedProjectId', 'groupedNominations'));
    }

    /**
     * حذف ترشيح (دفعة كاملة)
     */
    public function deleteBatch(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'user_id' => 'required',
            'created_at' => 'required',
        ]);

        // حذف المستفيدين الذين لم يستلموا بعد من المشروع نهائياً
        DB::table('project_beneficiaries')
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->where('created_at', $request->created_at)
            ->where('status', 'غير مستلم')
            ->delete();

        // سحب التكليف فقط ممن استلموا فعلياً (ليبقوا في سجلات المشروع)
        DB::table('project_beneficiaries')
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->where('created_at', $request->created_at)
            ->where('status', 'مستلم')
            ->update(['user_id' => null]);

        flash()->success('تم حذف مجموعة الترشيحات بنجاح');
        return back();
    }
    /**
     * تحديث بيانات دفعة (المحرز والملاحظات)
     */
    public function updateBatch(Request $request)
    {
        $request->validate([
            'project_id' => 'required',
            'user_id' => 'required',
            'created_at' => 'required',
            'captured_qty' => 'nullable|integer',
            'external_notes' => 'nullable|string',
        ]);

        DB::table('project_beneficiaries')
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->where('created_at', $request->created_at)
            ->update([
                'captured_qty' => (int) $request->captured_qty,
                'external_notes' => $request->external_notes,
            ]);

        flash()->success('تم تحديث بيانات الدفعة بنجاح');
        return back();
    }

    /**
     * تحديث طابع المشروع الزمني لإجبار التطبيق على إعادة المزامنة
     */
    public function refreshProject($id)
    {
        $project = Project::findOrFail($id);
        $project->touch();
        
        flash()->success('تم إرسال إشارة تحديث للمشروع بنجاح. سيظهر التحديث في تطبيق الجوال عند المزامنة القادمة.');
        return back();
    }
}
