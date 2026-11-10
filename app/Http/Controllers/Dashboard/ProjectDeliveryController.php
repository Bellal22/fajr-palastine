<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CustomProject;
use App\Models\Employee;
use App\Models\IncompletePerson;
use App\Models\Person;
use App\Models\Project;
use App\Models\SubWarehouse;
use App\Models\RrmBatch;
use App\Support\ExternalApiSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProjectDeliveryController extends Controller
{
    /**
     * Display the parcel/coupon delivery form page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->checkPermission();
        CustomProject::ensureTableExists();

        // Projects list (active ones first)
        $projects = Project::orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->get();

        $warehouses = SubWarehouse::orderBy('name')->get();
        $selectedProjectId = $request->query('project_id');
        $selectedCustomProjectId = $request->query('custom_project_id');
        $customProjects = CustomProject::where('is_active', true)
            ->orderByRaw("CASE WHEN slug = 'rrm' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->get();
        $rrmBatches = RrmBatch::orderBy('id', 'desc')->get();

        return view('dashboard.projects.deliveries.index', compact(
            'projects',
            'warehouses',
            'selectedProjectId',
            'selectedCustomProjectId',
            'customProjects',
            'rrmBatches'
        ));
    }

    /**
     * Real-time JavaScript AJAX check for a single ID against selected coupon(s).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkId(Request $request)
    {
        $this->checkPermission();
        Project::ensureCashColumnsExist();

        $request->validate([
            'id_num' => 'required|string',
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
        ]);

        $rawId = trim($request->id_num);
        $idNumber = preg_replace('/[^0-9]/', '', $rawId) ?: $rawId;
        $paddedId = str_pad($idNumber, 9, '0', STR_PAD_LEFT);

        $resolved = $this->resolvePerson($idNumber, $paddedId);

        if (!$resolved['found']) {
            return response()->json([
                'success' => false,
                'exists' => false,
                'message' => 'رقم الهوية (' . $idNumber . ') غير مسجل في النظام بتاتاً',
            ]);
        }

        $personData = $resolved['data'];
        $projectIds = $request->project_ids;
        $projects = Project::whereIn('id', $projectIds)->get()->keyBy('id');

        $couponsStatus = [];
        $eligibleCount = 0;
        $alreadyDeliveredCount = 0;
        $notAddedCount = 0;

        foreach ($projectIds as $pId) {
            $proj = $projects->get($pId);
            if (!$proj) continue;

            $record = $this->findPivotRecord($proj->id, $resolved);

            if ($record) {
                $warehouse = $record->sub_warehouse_id ? SubWarehouse::find($record->sub_warehouse_id) : null;
                $isDelivered = ($record->status === 'مستلم');

                if ($isDelivered) {
                    $alreadyDeliveredCount++;
                } else {
                    $eligibleCount++;
                }

                $couponsStatus[] = [
                    'project_id' => $proj->id,
                    'project_name' => $proj->name,
                    'is_cash' => (bool)$proj->is_cash,
                    'cash_amount_type' => $proj->cash_amount_type ?? 'fixed',
                    'project_cash_amount' => $proj->cash_amount,
                    'project_currency' => $proj->cash_currency ?? 'ILS',
                    'project_currency_symbol' => $proj->currency_symbol,
                    'cash_amount' => $record->cash_amount ?? ($proj->cash_amount_type === 'fixed' ? $proj->cash_amount : null),
                    'cash_currency' => $record->cash_currency ?? $proj->cash_currency ?? 'ILS',
                    'currency_symbol' => Project::CURRENCY_SYMBOLS[$record->cash_currency ?? $proj->cash_currency ?? 'ILS'] ?? '₪',
                    'delivery_method' => $record->delivery_method ?? $proj->default_delivery_method ?? 'cash',
                    'delivery_details' => $record->delivery_details ?? null,
                    'is_added' => true,
                    'record_id' => $record->id,
                    'status' => $record->status,
                    'status_label' => $isDelivered ? 'مستلم مسبقاً' : 'مستحق للتسليم',
                    'status_class' => $isDelivered ? 'warning' : 'success',
                    'quantity' => $record->quantity ?? 1,
                    'delivery_date' => $record->delivery_date,
                    'sub_warehouse_id' => $record->sub_warehouse_id,
                    'sub_warehouse_name' => $warehouse ? $warehouse->name : 'غير محدد',
                    'notes' => $record->notes,
                    'can_deliver' => !$isDelivered,
                ];
            } else {
                $notAddedCount++;
                $couponsStatus[] = [
                    'project_id' => $proj->id,
                    'project_name' => $proj->name,
                    'is_cash' => (bool)$proj->is_cash,
                    'cash_amount_type' => $proj->cash_amount_type ?? 'fixed',
                    'project_cash_amount' => $proj->cash_amount,
                    'project_currency' => $proj->cash_currency ?? 'ILS',
                    'project_currency_symbol' => $proj->currency_symbol,
                    'cash_amount' => ($proj->cash_amount_type === 'fixed' ? $proj->cash_amount : null),
                    'cash_currency' => $proj->cash_currency ?? 'ILS',
                    'currency_symbol' => $proj->currency_symbol,
                    'delivery_method' => $proj->default_delivery_method ?? 'cash',
                    'delivery_details' => null,
                    'is_added' => false,
                    'record_id' => null,
                    'status' => 'غير مضاف',
                    'status_label' => 'غير مضاف للكوبون',
                    'status_class' => 'danger',
                    'quantity' => 1,
                    'delivery_date' => null,
                    'sub_warehouse_id' => null,
                    'sub_warehouse_name' => '-',
                    'notes' => null,
                    'can_deliver' => false,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'exists' => true,
            'person' => $personData,
            'coupons' => $couponsStatus,
            'summary' => [
                'total_selected' => count($couponsStatus),
                'eligible_count' => $eligibleCount,
                'already_delivered_count' => $alreadyDeliveredCount,
                'not_added_count' => $notAddedCount,
                'has_any_eligible' => $eligibleCount > 0,
            ],
        ]);
    }

    /**
     * Batch check multiple ID numbers against selected coupon(s).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkBatch(Request $request)
    {
        $this->checkPermission();
        Project::ensureCashColumnsExist();

        $request->validate([
            'id_nums' => 'required|string',
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
        ]);

        $idNums = preg_split('/[\s,]+/', $request->id_nums, -1, PREG_SPLIT_NO_EMPTY);
        $projectIds = $request->project_ids;
        $projects = Project::whereIn('id', $projectIds)->get()->keyBy('id');

        $rows = [];
        $totalChecked = 0;
        $eligibleTotal = 0;
        $alreadyDeliveredTotal = 0;
        $notAddedTotal = 0;
        $notFoundTotal = 0;

        foreach ($idNums as $rawId) {
            $idNumber = preg_replace('/[^0-9]/', '', trim($rawId)) ?: trim($rawId);
            if (empty($idNumber)) continue;

            $totalChecked++;
            $paddedId = str_pad($idNumber, 9, '0', STR_PAD_LEFT);
            $resolved = $this->resolvePerson($idNumber, $paddedId);

            if (!$resolved['found']) {
                $notFoundTotal++;
                $rows[] = [
                    'id_num' => $idNumber,
                    'name' => 'غير موجود بالنظام',
                    'found' => false,
                    'status' => 'not_found',
                    'status_label' => 'غير مسجل بالنظام',
                    'status_class' => 'danger',
                    'coupons_summary' => 'غير مسجل',
                    'details' => [],
                ];
                continue;
            }

            $personData = $resolved['data'];
            $couponDetails = [];
            $hasEligible = false;
            $hasDelivered = false;
            $allNotAdded = true;

            foreach ($projectIds as $pId) {
                $proj = $projects->get($pId);
                if (!$proj) continue;

                $record = $this->findPivotRecord($proj->id, $resolved);

                if ($record) {
                    $allNotAdded = false;
                    $isDelivered = ($record->status === 'مستلم');
                    if ($isDelivered) {
                        $hasDelivered = true;
                        $alreadyDeliveredTotal++;
                    } else {
                        $hasEligible = true;
                        $eligibleTotal++;
                    }

                    $couponDetails[] = [
                        'project_id' => $proj->id,
                        'project_name' => $proj->name,
                        'is_cash' => (bool)$proj->is_cash,
                        'cash_amount_type' => $proj->cash_amount_type ?? 'fixed',
                        'cash_amount' => $record ? ($record->cash_amount ?? ($proj->cash_amount_type === 'fixed' ? $proj->cash_amount : null)) : ($proj->cash_amount_type === 'fixed' ? $proj->cash_amount : null),
                        'cash_currency' => $record ? ($record->cash_currency ?? $proj->cash_currency ?? 'ILS') : ($proj->cash_currency ?? 'ILS'),
                        'currency_symbol' => Project::CURRENCY_SYMBOLS[$record ? ($record->cash_currency ?? $proj->cash_currency ?? 'ILS') : ($proj->cash_currency ?? 'ILS')] ?? '₪',
                        'delivery_method' => $record ? ($record->delivery_method ?? $proj->default_delivery_method ?? 'cash') : ($proj->default_delivery_method ?? 'cash'),
                        'is_added' => true,
                        'status' => $record->status,
                        'quantity' => $record->quantity ?? 1,
                    ];
                } else {
                    $notAddedTotal++;
                    $couponDetails[] = [
                        'project_id' => $proj->id,
                        'project_name' => $proj->name,
                        'is_cash' => (bool)$proj->is_cash,
                        'cash_amount_type' => $proj->cash_amount_type ?? 'fixed',
                        'cash_amount' => ($proj->cash_amount_type === 'fixed' ? $proj->cash_amount : null),
                        'cash_currency' => $proj->cash_currency ?? 'ILS',
                        'currency_symbol' => $proj->currency_symbol,
                        'delivery_method' => $proj->default_delivery_method ?? 'cash',
                        'is_added' => false,
                        'status' => 'غير مضاف',
                        'quantity' => 1,
                    ];
                }
            }

            $overallStatus = 'not_added';
            $overallLabel = 'غير مضاف للكوبونات';
            $overallClass = 'danger';

            if ($hasEligible) {
                $overallStatus = 'eligible';
                $overallLabel = 'مستحق للتسليم';
                $overallClass = 'success';
            } elseif ($hasDelivered) {
                $overallStatus = 'delivered';
                $overallLabel = 'مستلم مسبقاً';
                $overallClass = 'warning';
            }

            $rows[] = [
                'id_num' => $personData['id_num'],
                'name' => $personData['name'],
                'found' => true,
                'status' => $overallStatus,
                'status_label' => $overallLabel,
                'status_class' => $overallClass,
                'is_relative' => $personData['is_relative'],
                'head_name' => $personData['head_name'],
                'details' => $couponDetails,
            ];
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'total' => $totalChecked,
                'eligible' => $eligibleTotal,
                'delivered' => $alreadyDeliveredTotal,
                'not_added' => $notAddedTotal,
                'not_found' => $notFoundTotal,
            ],
            'rows' => $rows,
        ]);
    }

    /**
     * Execute delivery for single or multiple coupons, single or batch IDs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function deliver(Request $request)
    {
        $this->checkPermission();
        Project::ensureCashColumnsExist();

        $request->validate([
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
            'id_nums' => 'required|string',
            'sub_warehouse_id' => 'nullable|exists:sub_warehouses,id',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'project_quantities' => 'nullable|array',
            'custom_quantities' => 'nullable|array',
            'delivery_method' => 'nullable|string|max:50',
            'cash_amount' => 'nullable|numeric|min:0',
            'cash_currency' => 'nullable|string|max:10',
            'delivery_details' => 'nullable|string|max:255',
            'custom_cash_amounts' => 'nullable|array',
            'custom_delivery_methods' => 'nullable|array',
            'custom_delivery_details' => 'nullable|array',
            'group_name' => 'nullable|string|max:190',
            'is_rrm' => 'nullable|boolean',
            'rrm_batch_name' => 'nullable|string|max:190',
            'auto_add_if_missing' => 'nullable|boolean',
            'skip_already_delivered' => 'nullable|boolean',
        ]);

        $projectIds = $request->project_ids;
        $projects = Project::whereIn('id', $projectIds)->get()->keyBy('id');
        $idNums = preg_split('/[\s,]+/', $request->id_nums, -1, PREG_SPLIT_NO_EMPTY);
        $subWarehouseId = $request->sub_warehouse_id;
        $deliveryDate = $request->delivery_date ?: now()->toDateString();
        $baseNotes = $request->filled('notes') ? trim($request->notes) : 'تسليم عبر واجهة تسليم الطرود';
        $quantity = $request->filled('quantity') ? (int)$request->quantity : 1;
        $projectQuantities = $request->input('project_quantities', []);
        $customQuantities = $request->input('custom_quantities', []);
        $deliveryMethod = $request->input('delivery_method');
        $cashAmount = $request->input('cash_amount');
        $cashCurrency = $request->input('cash_currency');
        $deliveryDetails = $request->input('delivery_details');
        $customCashAmounts = $request->input('custom_cash_amounts', []);
        $customDeliveryMethods = $request->input('custom_delivery_methods', []);
        $customDeliveryDetails = $request->input('custom_delivery_details', []);
        $groupName = $request->filled('group_name') ? trim($request->group_name) : null;
        $autoAdd = $request->boolean('auto_add_if_missing');
        $skipDelivered = $request->has('skip_already_delivered') ? $request->boolean('skip_already_delivered') : true;

        // Check if this is an RRM delivery batch
        $isRrm = $request->boolean('is_rrm')
            || $request->input('delivery_type') === 'rrm'
            || $request->input('delivery_mode') === 'rrm';

        $rrmBatch = null;
        $rrmBeneficiariesSet = [];
        $rrmTotalParcels = 0;

        if ($isRrm) {
            CustomProject::ensureTableExists();

            $customProject = null;
            if ($request->filled('custom_project_id')) {
                $customProject = CustomProject::find($request->input('custom_project_id'));
            }
            if (!$customProject) {
                $customProject = CustomProject::where('slug', 'rrm')->first() ?: CustomProject::first();
            }

            $prefix = $customProject && $customProject->code_prefix ? $customProject->code_prefix : 'RRM';

            // Check if existing batch ID was passed
            if ($request->filled('rrm_batch_id')) {
                $rrmBatch = RrmBatch::find($request->input('rrm_batch_id'));
            }

            // Or if existing batch name was passed
            $inputBatchName = trim($request->input('rrm_batch_name') ?: $groupName ?: '');
            if (!$rrmBatch && !empty($inputBatchName)) {
                $batchLookup = RrmBatch::where('name', $inputBatchName);
                if ($customProject) {
                    $batchLookup->where('custom_project_id', $customProject->id);
                }
                $rrmBatch = $batchLookup->latest()->first();
            }

            if ($rrmBatch) {
                // Update batch with any newly selected projects
                $existingIds = $rrmBatch->project_ids ?? [];
                $mergedIds = array_values(array_unique(array_merge($existingIds, $projectIds)));
                $rrmBatch->project_ids = $mergedIds;
                if ($customProject && empty($rrmBatch->custom_project_id)) {
                    $rrmBatch->custom_project_id = $customProject->id;
                }
                if ($subWarehouseId && empty($rrmBatch->sub_warehouse_id)) {
                    $rrmBatch->sub_warehouse_id = $subWarehouseId;
                }
                if ($deliveryDate) {
                    $rrmBatch->delivery_date = $deliveryDate;
                }
                $rrmBatch->save();
                $groupName = $rrmBatch->name;
            } else {
                $rrmCode = $prefix . '-' . date('Ymd') . '-' . mt_rand(1000, 9999);
                $rrmBatchName = $inputBatchName ?: ('كشف ' . $prefix . ' - ' . date('Y-m-d H:i'));
                $groupName = $groupName ?: $rrmBatchName;

                $rrmBatch = RrmBatch::create([
                    'custom_project_id' => $customProject ? $customProject->id : null,
                    'code' => $rrmCode,
                    'name' => $rrmBatchName,
                    'delivery_date' => $deliveryDate,
                    'sub_warehouse_id' => $subWarehouseId,
                    'user_id' => auth()->id(),
                    'project_ids' => $projectIds,
                    'project_quantities' => $projectQuantities ?: null,
                    'beneficiaries_count' => 0,
                    'total_parcels' => 0,
                    'notes' => $baseNotes,
                ]);
            }
        }

        $hasRrmBatchCol = Schema::hasColumn('project_beneficiaries', 'rrm_batch_id');

        $deliveredCount = 0;
        $alreadyDeliveredCount = 0;
        $addedAndDeliveredCount = 0;
        $notFoundCount = 0;
        $errors = [];
        $affectedProjects = [];

        DB::beginTransaction();
        try {
            foreach ($idNums as $rawId) {
                $idNumber = preg_replace('/[^0-9]/', '', trim($rawId)) ?: trim($rawId);
                if (empty($idNumber)) continue;

                $paddedId = str_pad($idNumber, 9, '0', STR_PAD_LEFT);
                $resolved = $this->resolvePerson($idNumber, $paddedId);

                if (!$resolved['found']) {
                    $notFoundCount++;
                    $errors[] = "الرقم {$idNumber}: غير مسجل في النظام";
                    continue;
                }

                $person = $resolved['person'];
                $targetPerson = $resolved['target_person'];
                $employee = $resolved['employee'];
                $incompletePerson = $resolved['incomplete_person'];

                // Check if specific granular actions were passed from UI
                $actions = $request->input('actions', []);

                // Check if this is a group delivery of multiple packages/coupons
                $isGroupDelivery = $isRrm
                    || ($request->input('delivery_mode') === 'multiple')
                    || (!empty($actions) && count($actions) > 1)
                    || (count($projectIds) > 1);

                $groupCode = $isGroupDelivery ? ('GRP-' . date('YmdHis') . '-' . mt_rand(1000, 9999)) : null;
                $groupTag = '';
                if ($groupCode) {
                    $groupTag = "[مجموعة:{$groupCode}" . ($groupName ? "|{$groupName}" : '') . "]";
                    if ($groupName) {
                        $groupTag .= " [اسم_المجموعة:{$groupName}]";
                    }
                }
                if ($rrmBatch) {
                    $groupTag = trim("{$groupTag} [RRM:{$rrmBatch->code}]");
                }

                $extractCashData = function($projId, $act = [], $rec = null) use (
                    $projects, $idNumber, $paddedId, $customDeliveryMethods, $customCashAmounts,
                    $customDeliveryDetails, $deliveryMethod, $cashAmount, $cashCurrency, $deliveryDetails
                ) {
                    $proj = $projects->get($projId);
                    if (!$proj || !$proj->is_cash) {
                        return [];
                    }

                    $method = $act['delivery_method']
                        ?? $customDeliveryMethods[$idNumber][$projId]
                        ?? $customDeliveryMethods[$paddedId][$projId]
                        ?? $deliveryMethod
                        ?? ($rec->delivery_method ?? null)
                        ?? $proj->default_delivery_method
                        ?? 'cash';

                    $amount = null;
                    if (isset($act['cash_amount']) && $act['cash_amount'] !== '') {
                        $amount = (float)$act['cash_amount'];
                    } elseif (isset($customCashAmounts[$idNumber][$projId]) && $customCashAmounts[$idNumber][$projId] !== '') {
                        $amount = (float)$customCashAmounts[$idNumber][$projId];
                    } elseif (isset($customCashAmounts[$paddedId][$projId]) && $customCashAmounts[$paddedId][$projId] !== '') {
                        $amount = (float)$customCashAmounts[$paddedId][$projId];
                    } elseif ($cashAmount !== null && $cashAmount !== '') {
                        $amount = (float)$cashAmount;
                    } elseif ($rec && $rec->cash_amount !== null) {
                        $amount = (float)$rec->cash_amount;
                    } elseif ($proj->cash_amount_type === 'fixed' && $proj->cash_amount !== null) {
                        $amount = (float)$proj->cash_amount;
                    }

                    $curr = $act['cash_currency']
                        ?? $cashCurrency
                        ?? ($rec->cash_currency ?? null)
                        ?? $proj->cash_currency
                        ?? 'ILS';

                    $details = $act['delivery_details']
                        ?? $customDeliveryDetails[$idNumber][$projId]
                        ?? $customDeliveryDetails[$paddedId][$projId]
                        ?? $deliveryDetails
                        ?? ($rec->delivery_details ?? null);

                    $fields = [
                        'delivery_method' => $method ?: 'cash',
                        'cash_currency' => $curr ?: 'ILS',
                    ];
                    if ($amount !== null) {
                        $fields['cash_amount'] = $amount;
                    }
                    if ($details !== null) {
                        $fields['delivery_details'] = $details;
                    }
                    return $fields;
                };

                if (!empty($actions) && is_array($actions)) {
                    foreach ($actions as $act) {
                        $pId = (int)($act['project_id'] ?? 0);
                        if (!$pId || !in_array($pId, $projectIds)) continue;

                        $affectedProjects[$pId] = true;
                        $record = $this->findPivotRecord($pId, $resolved);
                        $actType = $act['action'] ?? 'deliver';

                        if ($actType === 'increase') {
                            if ($record) {
                                $currentQty = (int)($record->quantity ?? 1);
                                $addQty = max(1, (int)($act['additional_quantity'] ?? 1));
                                $finalQty = $currentQty + $addQty;

                                $updatedNotes = trim(($record->notes ?? '') . ($groupTag && !str_contains($record->notes ?? '', $groupTag) ? " {$groupTag}" : '') . " | زيادة كمية: +{$addQty}");
                                if (!str_contains($updatedNotes, 'إعادة تكليف')) {
                                    $updatedNotes .= ' (إعادة تكليف)';
                                }

                                $updateData = [
                                    'quantity' => $finalQty,
                                    'status' => 'مستلم',
                                    'delivery_date' => $deliveryDate,
                                    'sub_warehouse_id' => $subWarehouseId ?: $record->sub_warehouse_id,
                                    'user_id' => auth()->id(),
                                    'notes' => $updatedNotes,
                                    'updated_at' => now(),
                                ];
                                $updateData = array_merge($updateData, $extractCashData($pId, $act, $record));
                                if ($rrmBatch && $hasRrmBatchCol) {
                                    $updateData['rrm_batch_id'] = $rrmBatch->id;
                                }
                                DB::table('project_beneficiaries')
                                    ->where('id', $record->id)
                                    ->update($updateData);

                                $deliveredCount++;
                                if ($rrmBatch) {
                                    $rrmBeneficiariesSet[$idNumber] = true;
                                    $rrmTotalParcels += $finalQty;
                                }
                            }
                        } elseif ($actType === 'add') {
                            $addQty = max(1, (int)($act['quantity'] ?? $quantity));
                            if ($record) {
                                // Already existed, just mark as delivered
                                $updatedNotes = trim(($groupTag && !str_contains($record->notes ?? '', $groupTag) ? "{$groupTag} " : '') . ($record->notes ?? '') . ' | ' . $baseNotes);
                                $updateData = [
                                    'status' => 'مستلم',
                                    'quantity' => $addQty,
                                    'delivery_date' => $deliveryDate,
                                    'sub_warehouse_id' => $subWarehouseId ?: $record->sub_warehouse_id,
                                    'user_id' => auth()->id(),
                                    'notes' => $updatedNotes,
                                    'updated_at' => now(),
                                ];
                                $updateData = array_merge($updateData, $extractCashData($pId, $act, $record));
                                if ($rrmBatch && $hasRrmBatchCol) {
                                    $updateData['rrm_batch_id'] = $rrmBatch->id;
                                }
                                DB::table('project_beneficiaries')
                                    ->where('id', $record->id)
                                    ->update($updateData);
                            } else {
                                $insertData = [
                                    'project_id' => $pId,
                                    'person_id' => $targetPerson ? $targetPerson->id : ($person ? $person->id : null),
                                    'employee_id' => $employee ? $employee->id : null,
                                    'incomplete_person_id' => $incompletePerson ? $incompletePerson->id : null,
                                    'status' => 'مستلم',
                                    'quantity' => $addQty,
                                    'delivery_date' => $deliveryDate,
                                    'sub_warehouse_id' => $subWarehouseId,
                                    'user_id' => auth()->id(),
                                    'notes' => trim(($groupTag ? "{$groupTag} " : '') . $baseNotes . ' (إضافة وتسليم)'),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                                $insertData = array_merge($insertData, $extractCashData($pId, $act, null));
                                if ($rrmBatch && $hasRrmBatchCol) {
                                    $insertData['rrm_batch_id'] = $rrmBatch->id;
                                }
                                DB::table('project_beneficiaries')->insert($insertData);
                                $addedAndDeliveredCount++;
                            }
                            $deliveredCount++;
                            if ($rrmBatch) {
                                $rrmBeneficiariesSet[$idNumber] = true;
                                $rrmTotalParcels += $addQty;
                            }
                        } elseif ($actType === 'deliver') {
                            if ($record) {
                                $deliverQty = max(1, (int)($act['quantity'] ?? ($quantity > 1 ? $quantity : ($record->quantity ?? 1))));
                                $updatedNotes = trim(($groupTag && !str_contains($record->notes ?? '', $groupTag) ? "{$groupTag} " : '') . ($record->notes ?? '') . ' | ' . $baseNotes);
                                $updateData = [
                                    'status' => 'مستلم',
                                    'quantity' => $deliverQty,
                                    'delivery_date' => $deliveryDate,
                                    'sub_warehouse_id' => $subWarehouseId ?: $record->sub_warehouse_id,
                                    'user_id' => auth()->id(),
                                    'notes' => $updatedNotes,
                                    'updated_at' => now(),
                                ];
                                $updateData = array_merge($updateData, $extractCashData($pId, $act, $record));
                                if ($rrmBatch && $hasRrmBatchCol) {
                                    $updateData['rrm_batch_id'] = $rrmBatch->id;
                                }
                                DB::table('project_beneficiaries')
                                    ->where('id', $record->id)
                                    ->update($updateData);
                                $deliveredCount++;
                                if ($rrmBatch) {
                                    $rrmBeneficiariesSet[$idNumber] = true;
                                    $rrmTotalParcels += $deliverQty;
                                }
                            }
                        }
                    }
                } else {
                    // Batch fallback
                    foreach ($projectIds as $pId) {
                        $affectedProjects[$pId] = true;
                        $record = $this->findPivotRecord($pId, $resolved);

                        // Determine parcel quantity: custom per-person > project-level default > global quantity > 1
                        $parcelQty = 1;
                        if (isset($customQuantities[$idNumber][$pId]) && is_numeric($customQuantities[$idNumber][$pId])) {
                            $parcelQty = max(1, (int)$customQuantities[$idNumber][$pId]);
                        } elseif (isset($customQuantities[$paddedId][$pId]) && is_numeric($customQuantities[$paddedId][$pId])) {
                            $parcelQty = max(1, (int)$customQuantities[$paddedId][$pId]);
                        } elseif (isset($projectQuantities[$pId]) && is_numeric($projectQuantities[$pId])) {
                            $parcelQty = max(1, (int)$projectQuantities[$pId]);
                        } elseif ($quantity > 1) {
                            $parcelQty = $quantity;
                        }

                        if ($record) {
                            if ($record->status === 'مستلم') {
                                $alreadyDeliveredCount++;
                                if ($skipDelivered) {
                                    // If this is an RRM delivery, make sure the beneficiary record is associated with the RRM batch and delivery_date is synced
                                    if ($rrmBatch && $hasRrmBatchCol) {
                                        $updateBeneficiaryData = ['rrm_batch_id' => $rrmBatch->id];
                                        if ($deliveryDate) {
                                            $updateBeneficiaryData['delivery_date'] = $deliveryDate;
                                        }
                                        if ($subWarehouseId && empty($record->sub_warehouse_id)) {
                                            $updateBeneficiaryData['sub_warehouse_id'] = $subWarehouseId;
                                        }
                                        DB::table('project_beneficiaries')
                                            ->where('id', $record->id)
                                            ->update($updateBeneficiaryData);

                                        $rrmBeneficiariesSet[$idNumber] = true;
                                        $rrmTotalParcels += max(1, (int)($record->quantity ?? $parcelQty));
                                    }
                                    continue;
                                }
                            }

                            $updatedNotes = trim(($groupTag && !str_contains($record->notes ?? '', $groupTag) ? "{$groupTag} " : '') . ($record->notes ?? '') . ' | ' . $baseNotes);
                            if ($record->status === 'مستلم' && !str_contains($updatedNotes, 'تكرار تسليم')) {
                                $updatedNotes .= ' (تكرار تسليم)';
                            }

                            $updateData = [
                                'status' => 'مستلم',
                                'quantity' => $parcelQty,
                                'delivery_date' => $deliveryDate,
                                'sub_warehouse_id' => $subWarehouseId ?: $record->sub_warehouse_id,
                                'user_id' => auth()->id(),
                                'notes' => $updatedNotes,
                                'updated_at' => now(),
                            ];
                            $updateData = array_merge($updateData, $extractCashData($pId, [], $record));
                            if ($rrmBatch && $hasRrmBatchCol) {
                                $updateData['rrm_batch_id'] = $rrmBatch->id;
                            }

                            DB::table('project_beneficiaries')
                                ->where('id', $record->id)
                                ->update($updateData);

                            $deliveredCount++;
                            if ($rrmBatch) {
                                $rrmBeneficiariesSet[$idNumber] = true;
                                $rrmTotalParcels += $parcelQty;
                            }
                        } else {
                            if ($autoAdd) {
                                $insertData = [
                                    'project_id' => $pId,
                                    'person_id' => $targetPerson ? $targetPerson->id : ($person ? $person->id : null),
                                    'employee_id' => $employee ? $employee->id : null,
                                    'incomplete_person_id' => $incompletePerson ? $incompletePerson->id : null,
                                    'status' => 'مستلم',
                                    'quantity' => $parcelQty,
                                    'delivery_date' => $deliveryDate,
                                    'sub_warehouse_id' => $subWarehouseId,
                                    'user_id' => auth()->id(),
                                    'notes' => trim(($groupTag ? "{$groupTag} " : '') . $baseNotes . ' (إضافة وتسليم فوري)'),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                                $insertData = array_merge($insertData, $extractCashData($pId, [], null));
                                if ($rrmBatch && $hasRrmBatchCol) {
                                    $insertData['rrm_batch_id'] = $rrmBatch->id;
                                }

                                DB::table('project_beneficiaries')->insert($insertData);
                                $deliveredCount++;
                                $addedAndDeliveredCount++;
                                if ($rrmBatch) {
                                    $rrmBeneficiariesSet[$idNumber] = true;
                                    $rrmTotalParcels += $parcelQty;
                                }
                            } else {
                                $errors[] = "الرقم {$idNumber}: غير مضاف للكوبون #{$pId}";
                            }
                        }
                    }
                }
            }


            DB::commit();

            // Trigger sync for affected projects
            foreach (array_keys($affectedProjects) as $projId) {
                try {
                    $proj = Project::find($projId);
                    if ($proj) {
                        ExternalApiSync::syncProject($proj);
                    }
                } catch (\Exception $e) {
                    Log::warning("ExternalApiSync error on project {$projId}: " . $e->getMessage());
                }
            }

            // Update RrmBatch statistics if created
            if ($rrmBatch) {
                $rrmBatch->recalculateStats();
            }

            $msg = "تمت عملية التسليم بنجاح: تم تسليم {$deliveredCount} طرد/كوبون";
            if ($rrmBatch) {
                $msg = "تم تسليم كشف مشروع الـ RRM ({$rrmBatch->name}) بنجاح: {$deliveredCount} طرد لـ " . count($rrmBeneficiariesSet) . " مستفيد";
            }
            if ($addedAndDeliveredCount > 0) {
                $msg .= " (منها {$addedAndDeliveredCount} إضافة وتسليم فوري)";
            }
            if ($alreadyDeliveredCount > 0) {
                $msg .= "، وتجاوز {$alreadyDeliveredCount} مسجلين كمستلم مسبقاً";
            }
            if ($notFoundCount > 0) {
                $msg .= "، و {$notFoundCount} أرقام غير موجودة";
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'delivered_count' => $deliveredCount,
                    'added_count' => $addedAndDeliveredCount,
                    'already_delivered_count' => $alreadyDeliveredCount,
                    'not_found_count' => $notFoundCount,
                    'errors' => $errors,
                    'is_rrm' => (bool)$rrmBatch,
                    'rrm_batch_id' => $rrmBatch ? $rrmBatch->id : null,
                    'rrm_batch_code' => $rrmBatch ? $rrmBatch->code : null,
                    'rrm_batch_name' => $rrmBatch ? $rrmBatch->name : null,
                    'rrm_batch_url' => $rrmBatch ? route('dashboard.rrm.show', $rrmBatch->id) : null,
                ]);
            }

            flash()->success($msg);
            if (!empty($errors)) {
                flash()->warning('تنبيهات أثناء العملية: ' . implode(' | ', array_slice($errors, 0, 5)));
            }

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delivery error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تنفيذ التسليم: ' . $e->getMessage(),
                ], 500);
            }

            flash()->error('حدث خطأ أثناء تنفيذ التسليم: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Resolve person or employee or incomplete person from ID number.
     *
     * @param string $idNumber
     * @param string $paddedId
     * @return array
     */
    protected function resolvePerson(string $idNumber, string $paddedId): array
    {
        $person = Person::where('id_num', $paddedId)->orWhere('id_num', $idNumber)->first();
        $employee = null;
        $incompletePerson = null;
        $targetPerson = null;
        $isRelative = false;
        $headPerson = null;

        if ($person) {
            $head = $person->findUltimateHead();
            $targetPerson = $head ?: $person;
            if ($head && $head->id !== $person->id) {
                $isRelative = true;
                $headPerson = $head;
            }
        } else {
            $employee = Employee::where('id_num', $paddedId)->orWhere('id_num', $idNumber)->first();
            if (!$employee) {
                $incompletePerson = IncompletePerson::where('id_num', $paddedId)->orWhere('id_num', $idNumber)->first();
            }
        }

        if (!$person && !$employee && !$incompletePerson) {
            return [
                'found' => false,
                'person' => null,
                'target_person' => null,
                'employee' => null,
                'incomplete_person' => null,
                'data' => null,
            ];
        }

        $name = '';
        $idNum = '';
        $phone = '-';
        $type = '';
        $typeLabel = '';
        $familyCount = 1;
        $blockName = '-';

        if ($person) {
            $name = $person->getFullName();
            $idNum = $person->id_num;
            $phone = $person->mobile ?: ($person->phone ?: '-');
            $type = 'person';
            $typeLabel = 'مستفيد مسجل';
            $familyCount = max(1, $targetPerson->relatives()->count() + 1);
            $blockName = $person->block?->name ?? '-';
        } elseif ($employee) {
            $name = $employee->name;
            $idNum = $employee->id_num;
            $phone = $employee->phone ?: '-';
            $type = 'employee';
            $typeLabel = 'موظف';
            $familyCount = 1;
        } else {
            $name = $incompletePerson->name;
            $idNum = $incompletePerson->id_num;
            $phone = $incompletePerson->mobile ?: '-';
            $type = 'incomplete_person';
            $typeLabel = 'بيانات غير مكتملة';
            $familyCount = 1;
        }

        return [
            'found' => true,
            'person' => $person,
            'target_person' => $targetPerson,
            'employee' => $employee,
            'incomplete_person' => $incompletePerson,
            'data' => [
                'id' => $person ? $person->id : ($employee ? $employee->id : $incompletePerson->id),
                'name' => $name,
                'id_num' => $idNum,
                'phone' => $phone,
                'type' => $type,
                'type_label' => $typeLabel,
                'family_count' => $familyCount,
                'block_name' => $blockName,
                'is_relative' => $isRelative,
                'head_name' => $headPerson ? $headPerson->getFullName() : null,
                'head_id_num' => $headPerson ? $headPerson->id_num : null,
            ],
        ];
    }

    /**
     * Find pivot record in project_beneficiaries for the resolved beneficiary.
     *
     * @param int $projectId
     * @param array $resolved
     * @return object|null
     */
    protected function findPivotRecord(int $projectId, array $resolved): ?object
    {
        $person = $resolved['person'];
        $targetPerson = $resolved['target_person'];
        $employee = $resolved['employee'];
        $incompletePerson = $resolved['incomplete_person'];

        $query = DB::table('project_beneficiaries')->where('project_id', $projectId);

        if ($person) {
            $pIds = array_unique(array_filter([$person->id, $targetPerson ? $targetPerson->id : null]));
            $query->whereIn('person_id', $pIds);
        } elseif ($employee) {
            $query->where('employee_id', $employee->id);
        } elseif ($incompletePerson) {
            $query->where('incomplete_person_id', $incompletePerson->id);
        }

        return $query->first();
    }

    /**
     * Check permission to access project deliveries.
     */
    protected function checkPermission()
    {
        if (!auth()->check()) {
            abort(401);
        }

        $user = auth()->user();
        if (
            !$user->isAdmin()
            && !$user->isSupervisor()
            && !$user->hasPermission('manage.projects')
            && !$user->hasPermission('manage.project_deliveries')
            && !$user->hasPermission('manage.custom_projects')
            && !$user->hasPermission('manage.rrm')
        ) {
            abort(403, 'غير مصرح لك بالوصول لإدارة تسليم الطرود والكوبونات');
        }
    }

    /**
     * Rename an existing group delivery by its group code.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function renameGroup(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'group_code' => 'required|string',
            'group_name' => 'required|string|max:190',
        ]);

        $groupCode = trim($request->group_code);
        $newName = trim($request->group_name);

        $records = DB::table('project_beneficiaries')
            ->where('notes', 'LIKE', "%[مجموعة:{$groupCode}%")
            ->get();

        if ($records->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على أي طرود مسجلة بهذا الرمز (' . $groupCode . ')',
            ], 404);
        }

        foreach ($records as $rec) {
            $notes = $rec->notes ?? '';
            // Update [مجموعة:CODE...] if present, or replace/add [اسم_المجموعة:...]
            if (preg_match('/\[مجموعة:[^\]]+\]/u', $notes)) {
                $notes = preg_replace('/\[مجموعة:[^\]]+\]/u', "[مجموعة:{$groupCode}|{$newName}]", $notes);
            }
            if (preg_match('/\[اسم_المجموعة:[^\]]+\]/u', $notes)) {
                $notes = preg_replace('/\[اسم_المجموعة:[^\]]+\]/u', "[اسم_المجموعة:{$newName}]", $notes);
            } else {
                $notes = trim($notes . " [اسم_المجموعة:{$newName}]");
            }

            DB::table('project_beneficiaries')
                ->where('id', $rec->id)
                ->update([
                    'notes' => $notes,
                    'updated_at' => now(),
                ]);
        }

        try {
            RrmBatch::where('code', $groupCode)->orWhere('id', $groupCode)->update(['name' => $newName]);
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل اسم المجموعة بنجاح إلى: ' . $newName,
            'group_code' => $groupCode,
            'new_name' => $newName,
        ]);
    }
}
