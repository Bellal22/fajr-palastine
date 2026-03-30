<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AreaResponsible;
use App\Models\Block;
use App\Models\Person;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileAppController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->user()->isAdmin()) {
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

        $currentAssignments = [];
        if ($selectedProjectId && $selectedUserId) {
            $currentAssignments = DB::table('project_beneficiaries')
                ->join('persons', 'persons.id', '=', 'project_beneficiaries.person_id')
                ->select('project_beneficiaries.*', 'persons.first_name', 'persons.father_name', 'persons.family_name', 'persons.id_num')
                ->where('project_beneficiaries.project_id', $selectedProjectId)
                ->where('project_beneficiaries.user_id', $selectedUserId)
                ->get();
        }

        return view('dashboard.mobile_app.nominate', compact('projects', 'users', 'subWarehouses', 'selectedProjectId', 'selectedUserId', 'currentAssignments'));
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
        ]);

        $project = Project::find($request->project_id);
        $conflictingProjectIds = $project->conflicts()->pluck('conflict_id')->toArray();
        $idNums = preg_split('/[\s,]+/', $request->id_nums, -1, PREG_SPLIT_NO_EMPTY);

        $imported = 0;
        $errors = [];
        $mergedCount = 0;
        $batchData = [];

        foreach ($idNums as $idNumber) {
            $idNumber = trim($idNumber);
            if (empty($idNumber)) continue;

            $paddedId = str_pad($idNumber, 9, '0', STR_PAD_LEFT);
            $person = Person::where('id_num', $paddedId)->first();

            if (!$person) {
                $errors[] = "الرقم {$idNumber}: لم يتم العثور على الشخص";
                continue;
            }

            $head = $person->findUltimateHead();
            if (!$head) $head = $person;

            if (isset($batchData[$head->id])) {
                $mergedCount++;
                continue;
            }

            // Check for project conflicts
            if (!empty($conflictingProjectIds)) {
                $hasConflict = DB::table('project_beneficiaries')
                    ->where('person_id', $head->id)
                    ->whereIn('project_id', $conflictingProjectIds)
                    ->exists();

                if ($hasConflict) {
                    $errors[] = "الرقم {$idNumber}: موجود مسبقاً في مشروع متعارض";
                    continue;
                }
            }

            // Check if already in THIS project (regardless of which user it is assigned to)
            $existingInSameProject = DB::table('project_beneficiaries')
                ->where('person_id', $head->id)
                ->where('project_id', $project->id)
                ->first();

            if ($existingInSameProject) {
                $statusMsg = $existingInSameProject->status == 'مستلم' ? " (مستلم مسبقاً)" : "";
                $errors[] = "الرقم {$idNumber}: مضاف مسبقاً لهذا المشروع{$statusMsg}";
                continue;
            }

            $batchData[$head->id] = [
                'user_id' => $request->user_id,
                'sub_warehouse_id' => $request->sub_warehouse_id,
                'status' => 'غير مستلم',
                'quantity' => 1,
                'notes' => 'تكليف موزع ميداني',
            ];
        }

        DB::beginTransaction();
        try {
            $batchTimestamp = now();
            foreach ($batchData as $personId => $pivotData) {
                $pivotData['created_at'] = $batchTimestamp;
                $pivotData['updated_at'] = $batchTimestamp;
                
                if ($project->beneficiaries()->where('person_id', $personId)->exists()) {
                    $project->beneficiaries()->updateExistingPivot($personId, $pivotData);
                } else {
                    $project->beneficiaries()->attach($personId, $pivotData);
                }
                $imported++;
            }
            DB::commit();

            $msg = "تمت المعالجة بنجاح. ";
            $msg .= "ناجح: {$imported}";
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
                ->join('persons', 'persons.id', '=', 'project_beneficiaries.person_id')
                ->leftJoin('users', 'users.id', '=', 'project_beneficiaries.user_id')
                ->leftJoin('sub_warehouses', 'sub_warehouses.id', '=', 'project_beneficiaries.sub_warehouse_id')
                ->where('project_beneficiaries.project_id', $selectedProjectId)
                ->whereNotNull('project_beneficiaries.user_id')
                ->select(
                    'project_beneficiaries.*', 
                    'persons.first_name', 'persons.father_name', 'persons.family_name', 'persons.id_num', 'persons.phone',
                    'users.name as worker_name',
                    'sub_warehouses.name as warehouse_name'
                )
                ->get();

            // Group by User, Warehouse and Date (approx)
            $groupedNominations = $rawNominations->groupBy(function($item) {
                return $item->user_id . '_' . $item->sub_warehouse_id . '_' . $item->created_at;
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

        DB::table('project_beneficiaries')
            ->where('project_id', $request->project_id)
            ->where('user_id', $request->user_id)
            ->where('created_at', $request->created_at)
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
}
