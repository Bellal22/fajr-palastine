<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Project;
use App\Models\Person;
use App\Models\ReadyPackage;
use App\Models\InternalPackage;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ProjectRequest;
use App\Models\AreaResponsible;
use App\Models\Block;
use App\Models\SubWarehouse;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProjectController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ProjectController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');

        $this->middleware('auth')->only([
            'beneficiaries',
            'importForm',
            'importBeneficiaries',
            'removeBeneficiary',
            'updateBeneficiaryStatus',
            'filterByAreasForm',
            'addBeneficiariesByAreas'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::filter()->latest()->paginate();

        return view('dashboard.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ProjectRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProjectRequest $request)
    {
        // التحقق من عدم وجود مشروع بنفس الاسم
        $existingProject = Project::where('name', $request->name)->first();

        if ($existingProject) {
            flash()->error('يوجد مشروع بنفس الاسم مسبقاً! الرجاء اختيار اسم آخر.');
            return redirect()->back()->withInput();
        }

        $project = Project::create($request->only([
            'name',
            'description',
            'start_date',
            'end_date',
            'status'
        ]));

        $this->syncRelations($project, $request);

        flash()->success(trans('projects.messages.created'));
        return redirect()->route('dashboard.projects.show', $project);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $project->load([
            'grantingEntities',
            'executingEntities',
            'couponTypes',
            'readyPackages',
            'internalPackages',
        ]);

        $project->loadCount([
            'beneficiaries as total_candidates',
            'beneficiaries as received_count' => function ($query) {
                $query->where('project_beneficiaries.status', 'مستلم');
            },
            'beneficiaries as not_received_count' => function ($query) {
                $query->where('project_beneficiaries.status', 'غير مستلم');
            }
        ]);

        // Get area breakdown for the integrated report panel
        $project->area_breakdown = DB::table('project_beneficiaries')
            ->join('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->leftJoin('persons as head', function($join) {
                $join->on('persons.relative_id', '=', 'head.id_num')
                     ->whereNull('head.relative_id');
            })
            ->where('project_beneficiaries.project_id', $project->id)
            ->where('project_beneficiaries.status', 'مستلم')
            ->select(
                DB::raw('COALESCE(persons.area_responsible_id, head.area_responsible_id) as area_id'),
                DB::raw('count(*) as count')
            )
            ->groupBy('area_id')
            ->pluck('count', 'area_id')
            ->toArray();

        $areas = AreaResponsible::whereIn('id', array_keys($project->area_breakdown))->get()->keyBy('id');

        return view('dashboard.projects.show', compact('project', 'areas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $project->load([
            'grantingEntities',
            'executingEntities',
            'couponTypes',
            'readyPackages',
            'internalPackages'
        ]);

        return view('dashboard.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\ProjectRequest $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProjectRequest $request, Project $project)
    {
        // التحقق من عدم وجود مشروع آخر بنفس الاسم
        $existingProject = Project::where('name', $request->name)
            ->where('id', '!=', $project->id)
            ->first();

        if ($existingProject) {
            flash()->error('يوجد مشروع آخر بنفس الاسم! الرجاء اختيار اسم آخر.');
            return redirect()->back()->withInput();
        }

        $project->update($request->only([
            'name',
            'description',
            'start_date',
            'end_date',
            'status'
        ]));

        $this->syncRelations($project, $request);

        flash()->success(trans('projects.messages.updated'));
        return redirect()->route('dashboard.projects.show', $project);
    }

    /**
     * Sync all relations.
     *
     * @param \App\Models\Project $project
     * @param \App\Http\Requests\Dashboard\ProjectRequest $request
     * @return void
     */
    private function syncRelations(Project $project, ProjectRequest $request)
    {
        // 1. Sync Partners
        DB::table('project_partners')->where('project_id', $project->id)->delete();

        if ($request->filled('granting_entities')) {
            foreach ($request->granting_entities as $id) {
                DB::table('project_partners')->insert([
                    'project_id' => $project->id,
                    'supplier_id' => $id,
                    'type' => 'granting',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        if ($request->filled('executing_entities')) {
            foreach ($request->executing_entities as $id) {
                DB::table('project_partners')->insert([
                    'project_id' => $project->id,
                    'supplier_id' => $id,
                    'type' => 'executing',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 2. Sync Coupon Types
        $couponTypesData = [];
        if ($request->filled('coupon_types')) {
            foreach ($request->coupon_types as $item) {
                if (isset($item['coupon_type_id']) && isset($item['quantity'])) {
                    $couponTypesData[$item['coupon_type_id']] = ['quantity' => $item['quantity']];
                }
            }
        }
        $project->couponTypes()->sync($couponTypesData);

        // 3. Sync Packages (Polymorphic)
        DB::table('project_packages')->where('project_id', $project->id)->delete();

        if ($request->filled('ready_packages')) {
            foreach ($request->ready_packages as $id) {
                DB::table('project_packages')->insert([
                    'project_id' => $project->id,
                    'packageable_id' => $id,
                    'packageable_type' => 'App\Models\ReadyPackage',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        if ($request->filled('internal_packages')) {
            foreach ($request->internal_packages as $id) {
                DB::table('project_packages')->insert([
                    'project_id' => $project->id,
                    'packageable_id' => $id,
                    'packageable_type' => 'App\Models\InternalPackage',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 4. Sync Conflicts
        if ($request->has('conflicts')) {
            $project->conflicts()->sync($request->conflicts);
            
            // جعل التعارض ثنائي الاتجاه (اختياري، لكن مفضل للمنطق الذي طلبه المستخدم)
            foreach ($request->conflicts as $conflictId) {
                $conflictProject = Project::find($conflictId);
                if ($conflictProject) {
                    $conflictProject->conflicts()->syncWithoutDetaching([$project->id]);
                }
            }
        } else {
            $project->conflicts()->detach();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Project $project
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        $project->delete();

        flash()->success(trans('projects.messages.deleted'));

        return redirect()->route('dashboard.projects.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Project::class);

        $projects = Project::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.projects.trashed', compact('projects'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Project $project)
    {
        $this->authorize('viewTrash', $project);

        return view('dashboard.projects.show', compact('project'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Project $project)
    {
        $this->authorize('restore', $project);

        $project->restore();

        flash()->success(trans('projects.messages.restored'));

        return redirect()->route('dashboard.projects.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Project $project
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Project $project)
    {
        $this->authorize('forceDelete', $project);

        $project->forceDelete();

        flash()->success(trans('projects.messages.deleted'));

        return redirect()->route('dashboard.projects.trashed');
    }

    /**
     * Display beneficiaries list.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function beneficiaries(Request $request, Project $project)
    {
        $query = $project->beneficiaries()
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id');

        // البحث برقم الهوية (يدعم أرقام متعددة مفصولة بمسافة أو فاصلة)
        if ($request->filled('search')) {
            $searchValue = $request->search;
            // تقسيم النص بناءً على المسافات، الفواصل، أو أسطر جديدة
            $ids = preg_split('/[\s,]+/', $searchValue, -1, PREG_SPLIT_NO_EMPTY);
            
            if (count($ids) > 1) {
                $query->whereIn('persons.id_num', $ids);
            } else {
                $query->where('persons.id_num', 'LIKE', "%{$searchValue}%");
            }
        }

        // فلتر حسب الحالة
        if ($request->filled('status')) {
            $query->wherePivot('status', $request->status);
        }

        // فلتر حسب تاريخ التسليم (من)
        if ($request->filled('date_from')) {
            $query->wherePivot('delivery_date', '>=', $request->date_from);
        }

        // فلتر حسب تاريخ التسليم (إلى)
        if ($request->filled('date_to')) {
            $query->wherePivot('delivery_date', '<=', $request->date_to);
        }

        $perPage = $request->get('per_page', 50);
    $beneficiaries = $query->paginate($perPage)->appends($request->all());

        // جلب كل المخازن مرة واحدة بدل استعلام لكل مستفيد
        $subWarehouseIds = $beneficiaries->pluck('pivot.sub_warehouse_id')->filter()->unique();
        $subWarehouses = SubWarehouse::whereIn('id', $subWarehouseIds)->get()->keyBy('id');

        return view('dashboard.projects.beneficiaries.index', compact('project', 'beneficiaries', 'subWarehouses'));
    }


    /**
     * Show import form.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function importForm(Project $project)
    {
        return view('dashboard.projects.beneficiaries.import', compact('project'));
    }

    /**
     * Import beneficiaries from Excel.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importBeneficiaries(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'sub_warehouse_id' => 'required|exists:sub_warehouses,id',
            'ignore_conflicts' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('file');
            $subWarehouseId = $request->sub_warehouse_id;
            $ignoreConflicts = $request->boolean('ignore_conflicts');

            // جلب المشاريع المتعارضة
            $conflictingProjectIds = $project->conflicts()->pluck('conflict_id')->toArray();

            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $header = $rows[0] ?? [];

            // تهيئة الفهارس الافتراضية
            $idIdx = 0;
            $qtyIdx = 3;
            $statusIdx = 5;
            $dateIdx = 7; // تاريخ التسليم
            $generalDateIdx = 4; // التاريخ العام
            $notesIdx = 6;

            // محاولة الكشف عن الفهارس من العناوين (Header)
            $hasHeader = false;
            foreach ($header as $i => $col) {
                $col = trim($col);
                if (empty($col)) continue;

                if (mb_strpos($col, 'هوية') !== false) {
                    $idIdx = $i;
                    $hasHeader = true;
                } elseif (mb_strpos($col, 'كمية') !== false) {
                    $qtyIdx = $i;
                    $hasHeader = true;
                } elseif (mb_strpos($col, 'حالة') !== false || mb_strpos($col, 'استلام') !== false) {
                    $statusIdx = $i;
                    $hasHeader = true;
                } elseif (mb_strpos($col, 'ملاحظات') !== false) {
                    $notesIdx = $i;
                    $hasHeader = true;
                } elseif (mb_strpos($col, 'تاريخ') !== false) {
                    if (mb_strpos($col, 'تسليم') !== false) {
                        $dateIdx = $i;
                    } else {
                        $generalDateIdx = $i;
                    }
                    $hasHeader = true;
                }
            }

            if ($hasHeader) {
                array_shift($rows);
            } else {
                // إذا لم يتم العثور على عناوين، نطبق كشفاً ذكياً على الصف الأول
                $firstId = trim($header[0] ?? '');
                if (is_numeric($firstId) && strlen($firstId) < 7 && !empty($header[1]) && strlen(trim($header[1])) >= 9) {
                    // تنسيق: مسلسل، هوية، اسم، جوال، منطقة، كمية، حالة، تاريخ، ملاحظات
                    $idIdx = 1;
                    $qtyIdx = 5;
                    $statusIdx = 6;
                    $dateIdx = 7;
                    $notesIdx = 8;
                }
            }

            $imported = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + ($hasHeader ? 2 : 1);
                $idNumber = trim($row[$idIdx] ?? '');

                $quantityValue = $row[$qtyIdx] ?? 1;
                $statusValue = $row[$statusIdx] ?? '';
                $deliveryDateValue = $row[$dateIdx] ?? '';
                $notesValue = $row[$notesIdx] ?? '';

                // إذا كان تاريخ التسليم المباشر فارغاً، نجرب عمود التاريخ العام
                if (empty(trim($deliveryDateValue)) || $deliveryDateValue == '-') {
                    $deliveryDateValue = $row[$generalDateIdx] ?? '';
                }

                if (empty($idNumber)) {
                    continue;
                }

                $person = Person::where('id_num', $idNumber)->first();

                if (!$person) {
                    $errors[] = "الصف {$rowNumber}: لم يتم العثور على الشخص برقم الهوية {$idNumber}";
                    continue;
                }

                // التحقق من التعارض
                if (!$ignoreConflicts && !empty($conflictingProjectIds)) {
                    $hasConflict = DB::table('project_beneficiaries')
                        ->where('person_id', $person->id)
                        ->whereIn('project_id', $conflictingProjectIds)
                        ->exists();

                    if ($hasConflict) {
                        $errors[] = "الصف {$rowNumber}: الشخص برقم الهوية {$idNumber} موجود مسبقاً في مشروع متعارض";
                        continue;
                    }
                }

                $status = trim($statusValue);
                // معالجة الحالة بمرونة أكبر (البحث عن كلمة مستلم داخل النص)
                if (mb_strpos($status, 'غير') !== false) {
                    $status = 'غير مستلم';
                } elseif (mb_strpos($status, 'مستلم') !== false) {
                    $status = 'مستلم';
                } else {
                    $status = 'غير مستلم';
                }

                // معالجة التاريخ بشكل أكثر مرونة وقوة
                $deliveryDate = null;
                $rawDateValue = trim($deliveryDateValue);
                if (empty($rawDateValue) || $rawDateValue == '-') {
                    $rawDateValue = trim($row[$generalDateIdx] ?? '');
                }

                if (!empty($rawDateValue) && $rawDateValue != '-') {
                    try {
                        if (is_numeric($rawDateValue) && $rawDateValue > 30000) {
                            // إذا كان التاريخ بصيغة الرقم (Excel Serial Date)
                            $deliveryDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDateValue)->format('Y-m-d');
                        } else {
                            // تنظيف النص من أي حروف زائدة قد تفسد التحليل
                            if (preg_match('/(\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/', $rawDateValue, $matches)) {
                                $deliveryDate = \Illuminate\Support\Carbon::parse($matches[1])->format('Y-m-d');
                            } else {
                                $deliveryDate = \Illuminate\Support\Carbon::parse($rawDateValue)->format('Y-m-d');
                            }
                        }
                    } catch (\Exception $e) {
                        // محاولة أخيرة باستخدام strtotime
                        $timestamp = strtotime($rawDateValue);
                        if ($timestamp) {
                            $deliveryDate = date('Y-m-d', $timestamp);
                        }
                    }
                }

                $pivotData = [
                    'quantity' => is_numeric($quantityValue) ? (int)$quantityValue : 1,
                    'status' => $status,
                    'notes' => ($notesValue == '-' || empty(trim($notesValue))) ? null : trim($notesValue),
                    'delivery_date' => $deliveryDate,
                    'sub_warehouse_id' => $subWarehouseId,
                ];

                if ($project->beneficiaries()->where('person_id', $person->id)->exists()) {
                    $project->beneficiaries()->updateExistingPivot($person->id, $pivotData);
                } else {
                    $project->beneficiaries()->attach($person->id, $pivotData);
                }

                $imported++;
            }

            DB::commit();

            if (count($errors) > 0) {
                flash()->warning("تم استيراد {$imported} مستفيد مع بعض الأخطاء");
                session()->flash('import_errors', $errors);
            } else {
                flash()->success("تم استيراد {$imported} مستفيد بنجاح");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.projects.beneficiaries', $project);
    }

    /**
     * Remove beneficiary from project.
     *
     * @param \App\Models\Project $project
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeBeneficiary(Project $project, Person $person)
    {
        $project->beneficiaries()->detach($person->id);

        flash()->success('تم حذف المستفيد بنجاح');
        return redirect()->route('dashboard.projects.beneficiaries', $project);
    }

    /**
     * Update beneficiary status.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBeneficiaryStatus(Request $request, Project $project, Person $person)
    {
        if (!$project->beneficiaries()->where('person_id', $person->id)->exists()) {
            flash()->error('هذا الشخص غير مضاف للمشروع');
            return redirect()->route('dashboard.projects.beneficiaries', $project);
        }

        $validated = $request->validate([
            'status' => 'required|in:مستلم,غير مستلم',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'delivery_date' => 'nullable|date',
        ]);

        try {
            $project->beneficiaries()->updateExistingPivot($person->id, [
                'status' => $validated['status'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
                'delivery_date' => $validated['delivery_date'] ?? null,
            ]);

            flash()->success('تم تحديث الحالة بنجاح');
        } catch (\Exception $e) {
            flash()->error('حدث خطأ: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.projects.beneficiaries', $project);
    }

    /**
     * Show filter form for adding beneficiaries by areas.
     *
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\Response
     */
    public function filterByAreasForm(Project $project)
    {
        $areaResponsibles = AreaResponsible::all();
        $blocks = Block::all();

        return view('dashboard.projects.beneficiaries.filter-areas', compact('project', 'areaResponsibles', 'blocks'));
    }

    /**
     * Add beneficiaries based on area filters.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addBeneficiariesByAreas(Request $request, Project $project)
    {
        $request->validate([
            'area_responsible_id' => 'required|exists:area_responsibles,id',
            'block_id' => 'required|exists:blocks,id',
            'quantity' => 'required|integer|min:1',
            'ignore_conflicts' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $ignoreConflicts = $request->boolean('ignore_conflicts');
            $conflictingProjectIds = $project->conflicts()->pluck('conflict_id')->toArray();

            // جلب جميع الأشخاص في المربع المحدد
            $people = Person::where('block_id', $request->block_id)->get();

            if ($people->isEmpty()) {
                flash()->warning('لا يوجد أشخاص في هذا المربع');
                return redirect()->back();
            }

            // استبعاد المضافين مسبقاً
            $existingBeneficiaryIds = $project->beneficiaries()->pluck('person_id')->toArray();

            $beneficiariesData = [];
            $addedCount = 0;

            foreach ($people as $person) {
                if (!in_array($person->id, $existingBeneficiaryIds)) {
                    // التحقق من التعارض
                    if (!$ignoreConflicts && !empty($conflictingProjectIds)) {
                        $hasConflict = DB::table('project_beneficiaries')
                            ->where('person_id', $person->id)
                            ->whereIn('project_id', $conflictingProjectIds)
                            ->exists();

                        if ($hasConflict) {
                            continue;
                        }
                    }

                    $beneficiariesData[$person->id] = [
                        'status' => 'غير مستلم',
                        'quantity' => $request->quantity,
                        'notes' => null,
                        'delivery_date' => null,
                    ];
                    $addedCount++;
                }
            }

            if ($addedCount == 0) {
                flash()->warning('جميع الأشخاص في هذا المربع مضافين مسبقاً');
                return redirect()->back();
            }

            $project->beneficiaries()->attach($beneficiariesData);

            DB::commit();

            flash()->success('تم إضافة ' . $addedCount . ' مستفيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('حدث خطأ: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.projects.beneficiaries', $project);
    }

    /**
     * Handle bulk actions for beneficiaries.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkBeneficiariesActions(Request $request, Project $project)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:persons,id',
            'action' => 'required|in:update_status,delete',
            // fields for update
            'status' => 'required_if:action,update_status|nullable|in:مستلم,غير مستلم',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $personIds = $request->items;

        try {
            DB::beginTransaction();

            if ($request->action === 'delete') {
                $project->beneficiaries()->detach($personIds);
                flash()->success('تم حذف المستفيدين المحددين بنجاح');
            } elseif ($request->action === 'update_status') {
                $updateData = [];
                if ($request->filled('status')) {
                    $updateData['status'] = $request->status;
                }
                if ($request->filled('delivery_date')) {
                    $updateData['delivery_date'] = $request->delivery_date;
                }
                if ($request->filled('quantity')) {
                    $updateData['quantity'] = $request->quantity;
                }
                if ($request->has('notes')) {
                    $updateData['notes'] = $request->notes;
                }

                if (!empty($updateData)) {
                    foreach ($personIds as $personId) {
                        $project->beneficiaries()->updateExistingPivot($personId, $updateData);
                    }
                }
                flash()->success('تم تحديث بيانات المستفيدين المحددين بنجاح');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('حدث خطأ: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
