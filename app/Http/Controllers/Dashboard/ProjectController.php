<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Project;
use App\Models\Person;
use App\Models\ReadyPackage;
use App\Models\InternalPackage;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\ProjectRequest;
use App\Jobs\ImportBeneficiariesJob;
use App\Models\AreaResponsible;
use App\Models\Block;
use App\Models\SubWarehouse;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProjectController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * ProjectController constructor.
     */
    public function __construct()
    {
        Project::ensureCashColumnsExist();
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
        $projects = Project::filter()
            ->with(['grantingEntities', 'executingEntities'])
            ->withCount([
                'beneficiaries as beneficiaries_count',
                'beneficiaries as received_count' => function ($query) {
                    $query->where('project_beneficiaries.status', 'مستلم');
                }
            ])
            ->withSum([
                'beneficiaries as total_quantity' => function ($query) {
                    $query->where('project_beneficiaries.status', 'مستلم');
                }
            ], 'project_beneficiaries.quantity')
            ->latest()
            ->paginate();

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

        Project::ensureCashColumnsExist();

        $isCash = $request->boolean('is_cash');
        $cashAmountType = $isCash ? $request->input('cash_amount_type', 'fixed') : 'fixed';

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status ?? 'active',
            'is_cash' => $isCash,
            'cash_amount_type' => $cashAmountType,
            'cash_amount' => ($isCash && $cashAmountType === 'fixed') ? $request->cash_amount : null,
            'cash_currency' => $isCash ? $request->cash_currency : null,
            'default_delivery_method' => $isCash ? $request->default_delivery_method : null,
        ]);

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

        $project->loadSum([
            'beneficiaries as total_quantity' => function ($query) {
                $query->where('project_beneficiaries.status', 'مستلم');
            }
        ], 'project_beneficiaries.quantity');

        // Get area breakdown for the integrated report panel
        $project->area_breakdown = DB::table('project_beneficiaries')
            ->join('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->leftJoin('persons as head', function ($join) {
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

        Project::ensureCashColumnsExist();

        $isCash = $request->boolean('is_cash');
        $cashAmountType = $isCash ? $request->input('cash_amount_type', 'fixed') : 'fixed';

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status ?? $project->status,
            'is_cash' => $isCash,
            'cash_amount_type' => $cashAmountType,
            'cash_amount' => ($isCash && $cashAmountType === 'fixed') ? $request->cash_amount : null,
            'cash_currency' => $isCash ? $request->cash_currency : null,
            'default_delivery_method' => $isCash ? $request->default_delivery_method : null,
        ]);

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
        if (!$request->boolean('is_cash') && $request->filled('coupon_types')) {
            foreach ($request->coupon_types as $item) {
                if (!empty($item['coupon_type_id']) && !empty($item['quantity'])) {
                    $couponTypesData[$item['coupon_type_id']] = ['quantity' => (int) $item['quantity']];
                }
            }
        }
        $project->couponTypes()->sync($couponTypesData);

        // 3. Sync Packages (Polymorphic)
        DB::table('project_packages')->where('project_id', $project->id)->delete();

        if (!$request->boolean('is_cash')) {
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
        }

        // 4. Sync Conflicts
        if ($request->has('conflicts')) {
            $project->conflicts()->sync($request->conflicts);

            // جعل التعارض ثنائي الاتجاه
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
        $perPage = $request->get('per_page', 50);

        $query = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->leftJoin('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->leftJoin('incomplete_people', 'project_beneficiaries.incomplete_person_id', '=', 'incomplete_people.id')
            ->leftJoin('employees', 'project_beneficiaries.employee_id', '=', 'employees.id')
            ->select(
                'project_beneficiaries.*',
                'project_beneficiaries.id as beneficiary_pivot_id',
                DB::raw('COALESCE(persons.id_num, incomplete_people.id_num, employees.id_num) as id_num'),
                DB::raw('COALESCE(persons.first_name, incomplete_people.first_name, employees.first_name) as first_name'),
                DB::raw('COALESCE(persons.father_name, incomplete_people.father_name, employees.father_name) as father_name'),
                DB::raw('COALESCE(persons.grandfather_name, incomplete_people.grandfather_name, employees.grandfather_name) as grandfather_name'),
                DB::raw('COALESCE(persons.family_name, incomplete_people.family_name, employees.family_name) as family_name'),
                DB::raw('COALESCE(persons.phone, incomplete_people.phone, employees.phone) as phone'),
                DB::raw('COALESCE(persons.current_city, incomplete_people.current_city, "غير محدد") as current_city'),
                DB::raw('COALESCE(persons.neighborhood, incomplete_people.neighborhood, "غير محدد") as neighborhood'),
                DB::raw('CASE WHEN persons.id IS NOT NULL THEN "person" WHEN incomplete_people.id IS NOT NULL THEN "incomplete" ELSE "employee" END as entity_type')
            );

        // البحث برقم الهوية (يبحث في الجداول الثلاثة عبر id_num المشترك)
        if ($request->filled('search')) {
            $searchValue = $request->search;
            $ids = preg_split('/[\s,]+/', $searchValue, -1, PREG_SPLIT_NO_EMPTY);
            
            $query->where(function($q) use ($ids, $searchValue) {
                if (count($ids) > 1) {
                    $q->whereIn('persons.id_num', $ids)
                      ->orWhereIn('incomplete_people.id_num', $ids)
                      ->orWhereIn('employees.id_num', $ids);
                } else {
                    $q->where('persons.id_num', 'LIKE', "%{$searchValue}%")
                      ->orWhere('incomplete_people.id_num', 'LIKE', "%{$searchValue}%")
                      ->orWhere('employees.id_num', 'LIKE', "%{$searchValue}%");
                }
            });
        }

        // الفلاتر الأخرى
        if ($request->filled('status')) {
            $query->where('project_beneficiaries.status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate(DB::raw('COALESCE(project_beneficiaries.delivery_date, project_beneficiaries.created_at)'), '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate(DB::raw('COALESCE(project_beneficiaries.delivery_date, project_beneficiaries.created_at)'), '<=', $request->date_to);
        }

        if ($request->filled('exact_date')) {
            $query->whereDate(DB::raw('COALESCE(project_beneficiaries.delivery_date, project_beneficiaries.created_at)'), '=', $request->exact_date);
        }

        if ($request->filled('quantity_from')) {
            $query->where('project_beneficiaries.quantity', '>=', $request->quantity_from);
        }

        if ($request->filled('quantity_to')) {
            $query->where('project_beneficiaries.quantity', '<=', $request->quantity_to);
        }

        if ($request->filled('sub_warehouse_id')) {
            $query->where('project_beneficiaries.sub_warehouse_id', $request->sub_warehouse_id);
        }

        if ($request->filled('exact_quantity')) {
            $query->where('project_beneficiaries.quantity', $request->exact_quantity);
        }

        $beneficiaries = $query->latest('project_beneficiaries.created_at')
            ->paginate($perPage)
            ->appends($request->all());

        $totalQuantity = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->sum('quantity');

        $subWarehouses = \App\Models\SubWarehouse::all()->keyBy('id');

        return view('dashboard.projects.beneficiaries.index', compact('project', 'beneficiaries', 'subWarehouses', 'totalQuantity'));
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
        Project::ensureCashColumnsExist();

        $request->validate([
            'file' => 'required_without:id_nums|nullable|mimes:xlsx,xls,csv|max:102400',
            'id_nums' => 'nullable|string',
            'manual_notes' => 'nullable|string',
            'sub_warehouse_id' => $project->is_cash ? 'nullable|exists:sub_warehouses,id' : 'required|exists:sub_warehouses,id',
            'delivery_method' => 'nullable|string|max:50',
            'cash_amount' => 'nullable|numeric|min:0',
            'cash_currency' => 'nullable|string|max:10',
            'delivery_details' => 'nullable|string|max:255',
            'ignore_conflicts' => 'nullable|boolean',
            'delivery_date' => 'nullable|date|required_with:id_nums',
            'status' => 'nullable|in:مستلم,غير مستلم|required_with:id_nums',
        ]);

        try {
            // Case 1: Manual Import via ID Numbers
            if ($request->filled('id_nums')) {
                DB::beginTransaction();

                $subWarehouseId = $request->sub_warehouse_id;
                $ignoreConflicts = $request->boolean('ignore_conflicts');
                $conflictingProjectIds = $project->conflicts()->pluck('conflict_id')->toArray();

                $status = $request->status;
                $deliveryDate = $request->delivery_date;
                $notes = $request->filled('manual_notes') ? $request->manual_notes : 'إدخال يدوي';
                $deliveryMethod = $request->input('delivery_method') ?: ($project->default_delivery_method ?: 'cash');
                $cashAmount = $request->filled('cash_amount') ? (float)$request->input('cash_amount') : ($project->cash_amount_type === 'fixed' ? $project->cash_amount : null);
                $cashCurrency = $request->input('cash_currency') ?: ($project->cash_currency ?: 'ILS');
                $deliveryDetails = $request->input('delivery_details');

                $idNums = preg_split('/[\s,]+/', $request->id_nums, -1, PREG_SPLIT_NO_EMPTY);
                $imported = 0;
                $errors = [];
                $batchData = [];
                $mergedCount = 0;

                $batchDataEmployee = [];

                foreach ($idNums as $idNumber) {
                    $idNumber = trim($idNumber);
                    if (empty($idNumber)) continue;

                    $paddedId = str_pad($idNumber, 9, '0', STR_PAD_LEFT);
                    $person = Person::where('id_num', $paddedId)->first();
                    $employee = \App\Models\Employee::where('id_num', $idNumber)->orWhere('id_num', $paddedId)->first();

                    if (!$person && !$employee) {
                        $errors[] = "الرقم {$idNumber}: لم يتم العثور على الشخص أو الموظف";
                        continue;
                    }

                    if ($employee) {
                        if (!$ignoreConflicts && !empty($conflictingProjectIds)) {
                            $hasConflict = DB::table('project_beneficiaries')
                                ->where('employee_id', $employee->id)
                                ->whereIn('project_id', $conflictingProjectIds)
                                ->exists();

                            if ($hasConflict) {
                                $errors[] = "الموظف {$idNumber}: موجود مسبقاً في مشروع متعارض";
                                continue;
                            }
                        }

                        $batchDataEmployee[$employee->id] = [
                            'employee' => $employee,
                            'quantity' => 1,
                            'status' => $status,
                            'notes' => $notes,
                            'delivery_date' => $deliveryDate,
                            'sub_warehouse_id' => $subWarehouseId,
                        ];
                    } else {
                        $head = $person->findUltimateHead();
                        if (!$head) $head = $person;

                        if (isset($batchData[$head->id])) {
                            $batchData[$head->id]['quantity'] += 1;
                            $mergedCount++;
                            continue;
                        }

                        if (!$ignoreConflicts && !empty($conflictingProjectIds)) {
                            $hasConflict = DB::table('project_beneficiaries')
                                ->where('person_id', $head->id)
                                ->whereIn('project_id', $conflictingProjectIds)
                                ->exists();

                            if ($hasConflict) {
                                $headIdNum = $head->id_num;
                                $errorMsg = "الرقم {$idNumber}: موجود مسبقاً في مشروع متعارض";
                                if ($headIdNum != $idNumber) $errorMsg .= " (رب الأسرة: {$headIdNum})";
                                $errors[] = $errorMsg;
                                continue;
                            }
                        }

                        $wivesCount = $head->wives()->count();
                        $batchData[$head->id] = [
                            'person' => $head,
                            'quantity' => max(1, $wivesCount),
                            'status' => $status,
                            'notes' => $notes,
                            'delivery_date' => $deliveryDate,
                            'sub_warehouse_id' => $subWarehouseId,
                        ];
                    }
                }

                foreach ($batchData as $data) {
                    $pivotData = [
                        'quantity' => $data['quantity'],
                        'status' => $data['status'],
                        'notes' => $data['notes'],
                        'delivery_date' => $data['delivery_date'],
                        'sub_warehouse_id' => $data['sub_warehouse_id'],
                    ];
                    if ($project->is_cash) {
                        $pivotData['delivery_method'] = $deliveryMethod;
                        if ($cashAmount !== null) {
                            $pivotData['cash_amount'] = $cashAmount;
                        }
                        $pivotData['cash_currency'] = $cashCurrency;
                        if ($deliveryDetails) {
                            $pivotData['delivery_details'] = $deliveryDetails;
                        }
                    }

                    if ($project->beneficiaries()->where('person_id', $data['person']->id)->exists()) {
                        $project->beneficiaries()->updateExistingPivot($data['person']->id, $pivotData);
                    } else {
                        $project->beneficiaries()->attach($data['person']->id, $pivotData);
                    }
                    $imported++;
                }

                foreach ($batchDataEmployee as $data) {
                    $pivotData = [
                        'quantity' => $data['quantity'],
                        'status' => $data['status'],
                        'notes' => $data['notes'],
                        'delivery_date' => $data['delivery_date'],
                        'sub_warehouse_id' => $data['sub_warehouse_id'],
                    ];
                    if ($project->is_cash) {
                        $pivotData['delivery_method'] = $deliveryMethod;
                        if ($cashAmount !== null) {
                            $pivotData['cash_amount'] = $cashAmount;
                        }
                        $pivotData['cash_currency'] = $cashCurrency;
                        if ($deliveryDetails) {
                            $pivotData['delivery_details'] = $deliveryDetails;
                        }
                    }

                    $exists = DB::table('project_beneficiaries')
                        ->where('project_id', $project->id)
                        ->where('employee_id', $data['employee']->id)
                        ->exists();

                    if ($exists) {
                        DB::table('project_beneficiaries')
                            ->where('project_id', $project->id)
                            ->where('employee_id', $data['employee']->id)
                            ->update(array_merge($pivotData, ['updated_at' => now()]));
                    } else {
                        DB::table('project_beneficiaries')->insert(array_merge([
                            'project_id' => $project->id,
                            'employee_id' => $data['employee']->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ], $pivotData));
                    }
                    $imported++;
                }

                DB::commit();

                $skipped = count($errors);
                $msg = "تم معالجة " . ($imported + $skipped + $mergedCount) . " طلب. ";
                $msg .= "النتائج: {$imported} ناجح، ";
                if ($skipped > 0) $msg .= "{$skipped} مستبعد، ";
                if ($mergedCount > 0) $msg .= "{$mergedCount} دمج عائلات. ";

                if ($skipped > 0) {
                    flash()->warning($msg);
                    session()->flash('import_errors', $errors);
                } else {
                    flash()->success($msg);
                }
            }
            // Case 2: Excel Import
            elseif ($request->hasFile('file')) {
                $path = $request->file('file')->store('imports/temp');

                // نمرر الـ ID فقط وليس كائن المشروع بالكامل
                ImportBeneficiariesJob::dispatch(
                    Storage::path($path),
                    $project->id,
                    $request->only(['sub_warehouse_id', 'ignore_conflicts', 'delivery_date', 'delivery_method', 'cash_amount', 'cash_currency', 'delivery_details'])
                );

                flash()->info('تم رفع الملف بنجاح، جاري المعالجة في الخلفية.');

                // التعديل المهم: إضافة with('importing', true) لتفعيل التحميل التلقائي
                return redirect()->route('dashboard.projects.beneficiaries', $project)->with('importing', true);
            }
        } catch (\Exception $e) {
            // في حال حدوث خطأ غير متوقع في الجزء اليدوي
            if (isset($batchData)) DB::rollBack();

            flash()->error('حدث خطأ: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.projects.beneficiaries', $project);
    }

    public function checkImportErrors(Project $project)
    {
        // 1. التحقق إذا كان انتهى بنجاح (بدون أخطاء)
        if ($project->import_error_file === 'completed') {
            return response()->json([
                'status' => 'finished',
                'has_errors' => false
            ]);
        }

        // 2. التحقق إذا كان يوجد ملف أخطاء
        $path = $project->import_error_file;
        if ($path && (Storage::disk('local')->exists($path) || file_exists(storage_path('app/' . $path)))) {
            return response()->json([
                'status' => 'finished',
                'has_errors' => true,
                'download_url' => route('dashboard.projects.download_errors', $project->id)
            ]);
        }

        // 3. لم ينتهِ بعد
        return response()->json([
            'status' => 'pending',
            'has_errors' => false
        ]);
    }

    public function downloadErrors(Project $project)
    {
        // الحصول على المسار من قاعدة البيانات
        $path = $project->import_error_file;

        // التحقق 1: هل يوجد مسار؟
        if (!$path) {
            return redirect()->back()->with('error', 'لا يوجد تقرير أخطاء.');
        }

        // التحقق 2: هل الملف موجود فعلياً؟
        if (!Storage::exists($path)) {
            // في حال كان المسار مخزناً بشكل كامل (نادراً)، نحاول طريقة أخرى
            if (file_exists(storage_path('app/' . $path))) {
                return response()->download(storage_path('app/' . $path));
            }
            return redirect()->back()->with('error', 'ملف الأخطاء غير موجود في السيرفر.');
        }

        // تحميل الملف ثم تنظيف الحقل (اختياري)
        // $project->import_error_file = null;
        // $project->save();

        return Storage::download($path, 'import_errors_project_' . $project->id . '.xlsx');
    }

    /**
     * Remove beneficiary from project.
     *
     * @param \App\Models\Project $project
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeBeneficiary(Project $project, $beneficiaryId)
    {
        DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('id', $beneficiaryId)
            ->delete();

        flash()->success('تم حذف المستفيد بنجاح');
        
        \App\Support\ExternalApiSync::syncProject($project);

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
    public function updateBeneficiaryStatus(Request $request, Project $project, $beneficiaryId)
    {
        $beneficiaryRecord = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('id', $beneficiaryId)
            ->first();

        if (!$beneficiaryRecord) {
            flash()->error('هذه العملية غير صحيحة أو السجل غير موجود');
            return redirect()->route('dashboard.projects.beneficiaries', $project);
        }

        $validated = $request->validate([
            'status' => 'required|in:مستلم,غير مستلم',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'delivery_method' => 'nullable|string|max:50',
            'cash_amount' => 'nullable|numeric|min:0',
            'cash_currency' => 'nullable|string|max:10',
            'delivery_details' => 'nullable|string|max:255',
        ]);

        try {
            Project::ensureCashColumnsExist();

            $status = $validated['status'];
            $quantity = $validated['quantity'];
            $notes = $validated['notes'];
            $delivery_date = $validated['delivery_date'];
            $delivery_method = $validated['delivery_method'] ?? null;
            $cash_amount = isset($validated['cash_amount']) && $validated['cash_amount'] !== '' ? $validated['cash_amount'] : null;
            $cash_currency = $validated['cash_currency'] ?? null;
            $delivery_details = $validated['delivery_details'] ?? null;

            // إذا تم تغيير الحالة إلى "مستلم"
            if ($status == 'مستلم') {
                // فحص وجود سجل استلام سابق (مستلم) لنفس الشخص في نفس المشروع بخلاف السجل الحالي
                $existing = DB::table('project_beneficiaries')
                    ->where('project_id', $project->id)
                    ->where('id', '!=', $beneficiaryId)
                    ->where(function($q) use ($beneficiaryRecord) {
                        if ($beneficiaryRecord->person_id) $q->where('person_id', $beneficiaryRecord->person_id);
                        elseif ($beneficiaryRecord->employee_id) $q->where('employee_id', $beneficiaryRecord->employee_id);
                        else $q->where('incomplete_person_id', $beneficiaryRecord->incomplete_person_id);
                    })
                    ->where('status', 'مستلم')
                    ->orderBy('id', 'asc') // نختار السجل الأول المضاف قديماً
                    ->first();

                if ($existing) {
                    // دمج البيانات في السجل الأول
                    $finalQty = (int)$existing->quantity + (int)$quantity;
                    $newNotes = trim(($existing->notes ?? '') . ' | ' . ($notes ?? ''));
                    if (!str_contains($newNotes, 'إعادة تكليف')) {
                        $newNotes .= ' (إعادة تكليف)';
                    }

                    $mergeUpdateData = [
                        'quantity' => $finalQty,
                        'notes' => $newNotes,
                        'sub_warehouse_id' => $beneficiaryRecord->sub_warehouse_id, // الحفاظ على المخزن
                        'delivery_date' => $delivery_date ?: $existing->delivery_date,
                        'updated_at' => now()
                    ];
                    if ($delivery_method) $mergeUpdateData['delivery_method'] = $delivery_method;
                    if ($cash_amount !== null) $mergeUpdateData['cash_amount'] = $cash_amount;
                    if ($cash_currency) $mergeUpdateData['cash_currency'] = $cash_currency;
                    if ($delivery_details) $mergeUpdateData['delivery_details'] = $delivery_details;

                    DB::table('project_beneficiaries')
                        ->where('id', $existing->id)
                        ->update($mergeUpdateData);
                    
                    // حذف السجل الحالي لأنه تم دمجه
                    DB::table('project_beneficiaries')->where('id', $beneficiaryId)->delete();
                    
                    flash()->success('تم اكتشاف سجل سابق لهذا المستفيد، وتم دمج الاستلام الجديد وزيادة الكمية بنجاح.');
                    return redirect()->route('dashboard.projects.beneficiaries', $project);
                }
            }

            // تحديث طبيعي إذا لم يوجد دمج أو الحالة ليست "مستلم"
            $singleUpdateData = [
                'status' => $status,
                'quantity' => $quantity,
                'notes' => $notes,
                'delivery_date' => $delivery_date,
                'updated_at' => now()
            ];
            if ($request->has('delivery_method')) {
                $singleUpdateData['delivery_method'] = $delivery_method;
            }
            if ($request->has('cash_amount')) {
                $singleUpdateData['cash_amount'] = $cash_amount;
            }
            if ($request->has('cash_currency')) {
                $singleUpdateData['cash_currency'] = $cash_currency;
            }
            if ($request->has('delivery_details')) {
                $singleUpdateData['delivery_details'] = $delivery_details;
            }

            DB::table('project_beneficiaries')
                ->where('id', $beneficiaryId)
                ->update($singleUpdateData);

            flash()->success('تم تحديث حالة المستفيد بنجاح');

            \App\Support\ExternalApiSync::syncProject($project);
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
        Project::ensureCashColumnsExist();

        $request->validate([
            'id_nums' => 'nullable|string',
            'area_responsible_id' => 'required_without:id_nums|nullable|exists:area_responsibles,id',
            'block_id' => 'required_without:id_nums|nullable|exists:blocks,id',
            'sub_warehouse_id' => $project->is_cash ? 'nullable|exists:sub_warehouses,id' : 'required|exists:sub_warehouses,id',
            'quantity' => $project->is_cash ? 'nullable|integer|min:1' : 'required|integer|min:1',
            'delivery_method' => 'nullable|string|max:50',
            'cash_amount' => 'nullable|numeric|min:0',
            'cash_currency' => 'nullable|string|max:10',
            'delivery_details' => 'nullable|string|max:255',
            'ignore_conflicts' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $ignoreConflicts = $request->boolean('ignore_conflicts');
            $conflictingProjectIds = $project->conflicts()->pluck('conflict_id')->toArray();

            if ($request->filled('id_nums')) {
                $idNums = preg_split('/[\s,]+/', $request->id_nums, -1, PREG_SPLIT_NO_EMPTY);
                $people = Person::whereIn('id_num', $idNums)->get();

                if ($people->isEmpty()) {
                    flash()->warning('لم يتم العثور على أي شخص بأرقام الهويات المدخلة');
                    return redirect()->back()->withInput();
                }
            } else {
                $people = Person::where('block_id', $request->block_id)->get();

                if ($people->isEmpty()) {
                    flash()->warning('لا يوجد أشخاص في هذا المربع');
                    return redirect()->back();
                }
            }

            $existingBeneficiaryIds = $project->beneficiaries()->pluck('person_id')->toArray();

            $beneficiariesData = [];
            $addedCount = 0;

            $cashAmt = null;
            if ($project->is_cash) {
                if ($request->filled('cash_amount')) {
                    $cashAmt = (float)$request->cash_amount;
                } elseif ($project->cash_amount_type === 'fixed') {
                    $cashAmt = $project->cash_amount;
                }
            }

            foreach ($people as $person) {
                if (!in_array($person->id, $existingBeneficiaryIds)) {
                    if (!$ignoreConflicts && !empty($conflictingProjectIds)) {
                        $hasConflict = DB::table('project_beneficiaries')
                            ->where('person_id', $person->id)
                            ->whereIn('project_id', $conflictingProjectIds)
                            ->exists();

                        if ($hasConflict) {
                            continue;
                        }
                    }

                    $pivotRecord = [
                        'status' => 'غير مستلم',
                        'quantity' => $request->quantity ?: 1,
                        'notes' => 'إضافة عبر ترشيح المناطق',
                        'delivery_date' => null,
                        'sub_warehouse_id' => $request->sub_warehouse_id,
                    ];
                    if ($project->is_cash) {
                        $pivotRecord['delivery_method'] = $request->delivery_method ?: ($project->default_delivery_method ?: 'cash');
                        if ($cashAmt !== null) {
                            $pivotRecord['cash_amount'] = $cashAmt;
                        }
                        $pivotRecord['cash_currency'] = $request->cash_currency ?: ($project->cash_currency ?: 'ILS');
                        if ($request->delivery_details) {
                            $pivotRecord['delivery_details'] = $request->delivery_details;
                        }
                    }

                    $beneficiariesData[$person->id] = $pivotRecord;
                    $addedCount++;
                }
            }

            if ($addedCount == 0) {
                flash()->warning('جميع الأشخاص المحددين مضافين مسبقاً أو تم استبعادهم بسبب التعارض');
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
            'items.*' => 'exists:project_beneficiaries,id',
            'action' => 'required|in:update_status,delete',
            'status' => 'required_if:action,update_status|nullable|in:مستلم,غير مستلم',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'delivery_method' => 'nullable|string|max:50',
            'cash_amount' => 'nullable|numeric|min:0',
            'cash_currency' => 'nullable|string|max:10',
        ]);

        $pivotIds = $request->items;

        try {
            DB::beginTransaction();

            if ($request->action === 'delete') {
                DB::table('project_beneficiaries')
                    ->where('project_id', $project->id)
                    ->whereIn('id', $pivotIds)
                    ->delete();
                flash()->success('تم حذف المستفيدين المحددين بنجاح');
            } elseif ($request->action === 'update_status') {
                Project::ensureCashColumnsExist();

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
                if ($request->has('delivery_method') && $request->filled('delivery_method')) {
                    $updateData['delivery_method'] = $request->delivery_method;
                }
                if ($request->has('cash_amount') && $request->filled('cash_amount')) {
                    $updateData['cash_amount'] = $request->cash_amount;
                }
                if ($request->has('cash_currency') && $request->filled('cash_currency')) {
                    $updateData['cash_currency'] = $request->cash_currency;
                }
                $updateData['updated_at'] = now();

                if (!empty($updateData)) {
                    DB::table('project_beneficiaries')
                        ->where('project_id', $project->id)
                        ->whereIn('id', $pivotIds)
                        ->update($updateData);
                    flash()->success('تم تحديث بيانات ' . count($pivotIds) . ' مستفيد بنجاح');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('حدث خطأ: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Export beneficiaries list to Excel.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportBeneficiaries(Request $request, Project $project)
    {
        $fileName = 'beneficiaries_' . $project->id . '_' . date('Y-m-d') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BeneficiariesExport($project, $request->all()), $fileName);
    }
}
