<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CustomProject;
use App\Models\RrmBatch;
use App\Models\SubWarehouse;
use App\Models\Project;
use App\Models\Person;
use App\Models\Employee;
use App\Models\IncompletePerson;
use App\Exports\RrmBatchBeneficiariesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class RrmController extends Controller
{
    /**
     * Check if user has permission.
     */
    protected function checkPermission()
    {
        if (auth()->check()) {
            $user = auth()->user();
            if (
                $user->isAdmin()
                || $user->hasPermission('manage.admins')
                || $user->hasPermission('manage.custom_projects')
                || $user->hasPermission('manage.rrm')
                || $user->hasPermission('manage.projects')
            ) {
                return true;
            }

            try {
                CustomProject::ensureTableExists();
                foreach (CustomProject::all() as $cp) {
                    if ($cp->userCanAccess($user)) {
                        return true;
                    }
                }
            } catch (\Throwable $e) {
            }
        }
        abort(403, 'غير مصرح لك بالوصول إلى إدارة مشروع الـ RRM');
    }

    /**
     * Display a listing of RRM batches / delivery sheets.
     */
    public function index(Request $request)
    {
        $this->checkPermission();
        CustomProject::ensureTableExists();

        $customProjects = CustomProject::orderByRaw("CASE WHEN slug = 'rrm' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->get();

        $currentCustomProject = null;
        if ($request->filled('custom_project_id')) {
            $currentCustomProject = $customProjects->firstWhere('id', (int) $request->custom_project_id);
        }

        $query = RrmBatch::query()->with(['subWarehouse', 'user', 'customProject']);

        if ($currentCustomProject) {
            $query->where('custom_project_id', $currentCustomProject->id);
        }

        // Search by name or code
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        // Filter by warehouse
        if ($request->filled('sub_warehouse_id')) {
            $query->where('sub_warehouse_id', $request->sub_warehouse_id);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }

        // Auto-heal any batches where beneficiaries_count or total_parcels is 0
        $uncountedBatches = RrmBatch::where('beneficiaries_count', 0)->orWhere('total_parcels', 0)->get();
        foreach ($uncountedBatches as $ub) {
            $ub->recalculateStats();
        }

        // Summary statistics (scoped to selected custom project if selected)
        $statsQuery = RrmBatch::query();
        if ($currentCustomProject) {
            $statsQuery->where('custom_project_id', $currentCustomProject->id);
        }
        $totalBatches = (clone $statsQuery)->count();
        $totalBeneficiaries = (int) (clone $statsQuery)->sum('beneficiaries_count');
        $totalParcels = (int) (clone $statsQuery)->sum('total_parcels');

        $batches = $query->orderBy('id', 'desc')->paginate(15)->appends($request->query());
        $warehouses = SubWarehouse::pluck('name', 'id');

        return view('dashboard.rrm.index', compact(
            'batches',
            'totalBatches',
            'totalBeneficiaries',
            'totalParcels',
            'warehouses',
            'customProjects',
            'currentCustomProject'
        ));
    }

    /**
     * Display a specific RRM batch and its beneficiaries.
     */
    public function show(Request $request, $id)
    {
        $this->checkPermission();
        RrmBatch::ensureTableExists();

        $batch = RrmBatch::with(['subWarehouse', 'user'])->where('id', $id)->orWhere('code', $id)->firstOrFail();
        $batch->recalculateStats();

        $projectsMap = Project::pluck('name', 'id')->toArray();
        $warehousesMap = SubWarehouse::pluck('name', 'id')->toArray();

        $beneficiaries = $this->resolveBatchBeneficiaries($batch, $projectsMap, $warehousesMap, $request->input('search'));

        // Quick breakdown of parcels in this batch
        $parcelsSummary = [];
        foreach ($beneficiaries as $b) {
            foreach ($b['parcels'] as $p) {
                $pName = $p['project_name'];
                $parcelsSummary[$pName] = ($parcelsSummary[$pName] ?? 0) + $p['quantity'];
            }
        }

        return view('dashboard.rrm.show', compact('batch', 'beneficiaries', 'parcelsSummary'));
    }

    /**
     * Export the beneficiaries of this RRM sheet to Excel.
     */
    public function export(Request $request, $id)
    {
        $this->checkPermission();
        RrmBatch::ensureTableExists();

        $batch = RrmBatch::where('id', $id)->orWhere('code', $id)->firstOrFail();
        $batch->recalculateStats();

        $projectsMap = Project::pluck('name', 'id')->toArray();
        $warehousesMap = SubWarehouse::pluck('name', 'id')->toArray();

        $beneficiaries = $this->resolveBatchBeneficiaries($batch, $projectsMap, $warehousesMap, null);

        $safeCode = preg_replace('/[^A-Za-z0-9_-]/', '_', $batch->code);
        $fileName = "RRM_{$safeCode}.xlsx";

        return Excel::download(new RrmBatchBeneficiariesExport($batch, $beneficiaries), $fileName);
    }

    /**
     * Print-friendly view of the RRM sheet.
     */
    public function print(Request $request, $id)
    {
        $this->checkPermission();
        RrmBatch::ensureTableExists();

        $batch = RrmBatch::with(['subWarehouse', 'user'])->where('id', $id)->orWhere('code', $id)->firstOrFail();
        $batch->recalculateStats();

        $projectsMap = Project::pluck('name', 'id')->toArray();
        $warehousesMap = SubWarehouse::pluck('name', 'id')->toArray();

        $beneficiaries = $this->resolveBatchBeneficiaries($batch, $projectsMap, $warehousesMap, $request->input('search'));

        // Quick breakdown of parcels and items in this batch
        $parcelsSummary = [];
        foreach ($beneficiaries as $b) {
            foreach ($b['parcels'] as $p) {
                $pName = $p['project_name'];
                $parcelsSummary[$pName] = ($parcelsSummary[$pName] ?? 0) + $p['quantity'];
            }
        }

        return view('dashboard.rrm.print', compact('batch', 'beneficiaries', 'parcelsSummary'));
    }

    /**
     * Update the RRM sheet name and notes.
     */
    public function update(Request $request, $id)
    {
        $this->checkPermission();
        RrmBatch::ensureTableExists();

        $batch = RrmBatch::where('id', $id)->orWhere('code', $id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:190',
            'delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $newName = trim($request->name);
        $batch->name = $newName;
        if ($request->filled('delivery_date')) {
            $batch->delivery_date = $request->delivery_date;
        }
        $batch->notes = $request->notes;
        $batch->save();

        // Update tags and delivery_date in project_beneficiaries matching this batch code
        $code = $batch->code;
        $rows = DB::table('project_beneficiaries')
            ->where('notes', 'LIKE', "%[RRM:{$code}%")
            ->orWhere('notes', 'LIKE', "%[مجموعة:{$code}%");

        if (Schema::hasColumn('project_beneficiaries', 'rrm_batch_id')) {
            $rows->orWhere('rrm_batch_id', $batch->id);
        }

        $records = $rows->get();
        foreach ($records as $rec) {
            $n = $rec->notes ?? '';
            if (preg_match('/\[اسم_المجموعة:[^\]]+\]/u', $n)) {
                $n = preg_replace('/\[اسم_المجموعة:[^\]]+\]/u', "[اسم_المجموعة:{$newName}]", $n);
            }
            if (preg_match('/\[مجموعة:[^\|\]]+\|([^\]]+)\]/u', $n)) {
                $n = preg_replace('/\[مجموعة:([^\|\]]+)\|[^\]]+\]/u', "[مجموعة:$1|{$newName}]", $n);
            }
            $upData = ['notes' => $n];
            if ($request->filled('delivery_date')) {
                $upData['delivery_date'] = $request->delivery_date;
            }
            DB::table('project_beneficiaries')->where('id', $rec->id)->update($upData);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات كشف الـ RRM بنجاح',
                'name' => $newName,
                'delivery_date' => $batch->delivery_date ? $batch->delivery_date->format('Y-m-d') : null,
            ]);
        }

        flash()->success('تم تحديث بيانات الكشف بنجاح');
        return redirect()->back();
    }

    /**
     * Delete an RRM batch.
     */
    public function destroy(Request $request, $id)
    {
        $this->checkPermission();
        RrmBatch::ensureTableExists();

        $batch = RrmBatch::where('id', $id)->orWhere('code', $id)->firstOrFail();

        // Check if user also wants to reset / delete beneficiary delivery records
        if ($request->input('revert_deliveries') == 1) {
            $q = DB::table('project_beneficiaries');
            if (Schema::hasColumn('project_beneficiaries', 'rrm_batch_id')) {
                $q->where('rrm_batch_id', $batch->id)->orWhere('notes', 'LIKE', "%[RRM:{$batch->code}%");
            } else {
                $q->where('notes', 'LIKE', "%[RRM:{$batch->code}%");
            }
            $q->delete();
        } else {
            // Just clear batch relationship
            if (Schema::hasColumn('project_beneficiaries', 'rrm_batch_id')) {
                DB::table('project_beneficiaries')->where('rrm_batch_id', $batch->id)->update(['rrm_batch_id' => null]);
            }
        }

        $batch->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف كشف الـ RRM بنجاح',
            ]);
        }

        flash()->success('تم حذف كشف الـ RRM بنجاح');
        return redirect()->route('dashboard.rrm.index');
    }

    /**
     * Helper to resolve grouped beneficiaries for a batch.
     */
    protected function resolveBatchBeneficiaries(RrmBatch $batch, array $projectsMap, array $warehousesMap, ?string $search = null)
    {
        $rows = $batch->getBeneficiaryRowsQuery()->get();

        // Collect distinct person IDs, employee IDs, incomplete person IDs
        $personIds = $rows->pluck('person_id')->filter()->unique()->toArray();
        $employeeIds = $rows->pluck('employee_id')->filter()->unique()->toArray();
        $incPersonIds = $rows->pluck('incomplete_person_id')->filter()->unique()->toArray();
        $projectIds = $rows->pluck('project_id')->filter()->unique()->toArray();

        $persons = !empty($personIds)
            ? Person::with(['areaResponsible', 'block'])->whereIn('id', $personIds)->get()->keyBy('id')
            : collect();

        $employees = !empty($employeeIds)
            ? Employee::whereIn('id', $employeeIds)->get()->keyBy('id')
            : collect();

        $incPersons = !empty($incPersonIds)
            ? IncompletePerson::whereIn('id', $incPersonIds)->get()->keyBy('id')
            : collect();

        // Fetch coupon types quantities for all projects linked to this batch (الكمية من الأنواع)
        $projectCouponTypes = collect();
        if (!empty($projectIds)) {
            $projectCouponTypes = DB::table('project_coupon_types as pct')
                ->join('coupon_types as ct', 'pct.coupon_type_id', '=', 'ct.id')
                ->whereIn('pct.project_id', $projectIds)
                ->select('pct.project_id', 'pct.coupon_type_id', 'ct.name as type_name', 'pct.quantity as type_quantity')
                ->get()
                ->groupBy('project_id');
        }

        // Group rows by beneficiary
        $grouped = collect();

        foreach ($rows as $row) {
            $key = null;
            $name = '-';
            $idNum = '-';
            $phone = '-';
            $areaName = '-';
            $profileUrl = null;

            if ($row->person_id && isset($persons[$row->person_id])) {
                $p = $persons[$row->person_id];
                $key = 'person_' . $p->id;
                $name = $p->getFullQuadName() ?: $p->getFullName();
                $idNum = $p->id_num;
                $phone = $p->phone;
                $areaName = ($p->areaResponsible->name ?? '') . ($p->block ? ' - ' . $p->block->name : '');
                $profileUrl = route('dashboard.people.show', $p->id);
            } elseif ($row->employee_id && isset($employees[$row->employee_id])) {
                $e = $employees[$row->employee_id];
                $key = 'emp_' . $e->id;
                $name = $e->getFullName() ?: ($e->name ?? 'موظف');
                $idNum = $e->id_num ?? '-';
                $phone = $e->phone ?? '-';
                $areaName = 'فريق العمل';
                $profileUrl = route('dashboard.employees.show', $e->id);
            } elseif ($row->incomplete_person_id && isset($incPersons[$row->incomplete_person_id])) {
                $inc = $incPersons[$row->incomplete_person_id];
                $key = 'inc_' . $inc->id;
                $name = $inc->getFullQuadName() ?: ($inc->name ?? 'غير مكتمل البيانات');
                $idNum = $inc->id_num ?? '-';
                $phone = $inc->phone ?? '-';
                $areaName = 'غير مكتمل';
                $profileUrl = route('dashboard.incomplete-people.show', $inc->id);
            } else {
                $key = 'unknown_' . $row->id;
            }

            $whName = $row->sub_warehouse_id && isset($warehousesMap[$row->sub_warehouse_id])
                ? $warehousesMap[$row->sub_warehouse_id]
                : '-';

            $projName = isset($projectsMap[$row->project_id]) ? $projectsMap[$row->project_id] : ('مشروع #' . $row->project_id);

            $bDeliveryDate = $row->delivery_date 
                ? \Carbon\Carbon::parse($row->delivery_date)->format('Y-m-d') 
                : ($batch->delivery_date ? $batch->delivery_date->format('Y-m-d') : date('Y-m-d'));

            $rowNote = '';
            if (!empty($row->notes)) {
                if (preg_match('/\[ملاحظة:([^\]]+)\]/u', $row->notes, $m)) {
                    $rowNote = trim($m[1]);
                } else {
                    $clean = preg_replace('/\[[^\]]+\]/u', '', $row->notes);
                    $clean = trim(str_replace(['تسليم عبر واجهة تسليم الطرود', '|', '(إضافة وتسليم)'], '', $clean));
                    $rowNote = $clean;
                }
            }

            if (!$grouped->has($key)) {
                $grouped->put($key, [
                    'key' => $key,
                    'row_ids' => [$row->id],
                    'name' => $name,
                    'id_num' => $idNum,
                    'phone' => $phone,
                    'area_name' => trim($areaName, ' -') ?: '-',
                    'warehouse_name' => $whName,
                    'delivery_date' => $bDeliveryDate,
                    'status' => $row->status ?? 'مستلم',
                    'notes' => $rowNote,
                    'profile_url' => $profileUrl,
                    'parcels' => [],
                    'total_quantity' => 0,
                    'parcels_summary' => '',
                ]);
            } else {
                $current = $grouped->get($key);
                $current['row_ids'][] = $row->id;
                if (!empty($rowNote) && empty($current['notes'])) {
                    $current['notes'] = $rowNote;
                }
                $grouped->put($key, $current);
            }

            $current = $grouped->get($key);

            // Determine item quantity based on type quantity in bundle (الكمية من الأنواع مش من الطرد)
            $rowQty = max(1, (int)($row->quantity ?? 1));
            $batchQuantities = $batch->project_quantities ?? [];

            // 1. Quantity from project coupon types (الأنواع مش من الطرد)
            if (isset($projectCouponTypes[$row->project_id]) && $projectCouponTypes[$row->project_id]->isNotEmpty()) {
                $types = $projectCouponTypes[$row->project_id];
                if ($types->count() === 1) {
                    $t = $types->first();
                    $typeBaseQty = max(1, (int)$t->type_quantity);
                    $finalQty = $typeBaseQty * $rowQty;

                    $current['parcels'][] = [
                        'project_id' => $row->project_id,
                        'project_name' => $projName,
                        'type_name' => $t->type_name,
                        'quantity' => $finalQty,
                        'status' => $row->status ?? 'مستلم',
                    ];
                    $current['total_quantity'] += $finalQty;
                } else {
                    foreach ($types as $t) {
                        $typeBaseQty = max(1, (int)$t->type_quantity);
                        $finalQty = $typeBaseQty * $rowQty;

                        $current['parcels'][] = [
                            'project_id' => $row->project_id,
                            'project_name' => "{$projName} - {$t->type_name}",
                            'type_name' => $t->type_name,
                            'quantity' => $finalQty,
                            'status' => $row->status ?? 'مستلم',
                        ];
                        $current['total_quantity'] += $finalQty;
                    }
                }
            } elseif (!empty($batchQuantities) && isset($batchQuantities[$row->project_id]) && is_numeric($batchQuantities[$row->project_id])) {
                // 2. Quantity from custom batch project_quantities
                $bQty = max(1, (int)$batchQuantities[$row->project_id]);
                $finalQty = ($bQty > 1) ? (($rowQty === 1) ? $bQty : ($rowQty * $bQty)) : $rowQty;

                $current['parcels'][] = [
                    'project_id' => $row->project_id,
                    'project_name' => $projName,
                    'quantity' => $finalQty,
                    'status' => $row->status ?? 'مستلم',
                ];
                $current['total_quantity'] += $finalQty;
            } else {
                // 3. Fallback: Parcel count
                $finalQty = $rowQty;
                $current['parcels'][] = [
                    'project_id' => $row->project_id,
                    'project_name' => $projName,
                    'quantity' => $finalQty,
                    'status' => $row->status ?? 'مستلم',
                ];
                $current['total_quantity'] += $finalQty;
            }

            $grouped->put($key, $current);
        }

        // Build parcel summary string for each beneficiary
        $result = $grouped->map(function ($item) {
            $parts = [];
            foreach ($item['parcels'] as $p) {
                $parts[] = "{$p['project_name']} ({$p['quantity']})";
            }
            $item['parcels_summary'] = implode(' + ', $parts);
            return $item;
        })->values();

        // Optional search filter within the sheet
        if (!empty($search)) {
            $s = trim($search);
            $result = $result->filter(function ($item) use ($s) {
                return str_contains($item['id_num'], $s)
                    || str_contains($item['name'], $s)
                    || str_contains($item['phone'], $s)
                    || str_contains($item['area_name'], $s)
                    || str_contains($item['notes'] ?? '', $s);
            })->values();
        }

        return $result;
    }

    /**
     * Update a specific beneficiary note inside this batch.
     */
    public function updateBeneficiaryNote(Request $request, $id)
    {
        $this->checkPermission();

        $request->validate([
            'row_ids' => 'required|string',
            'note' => 'nullable|string|max:500',
        ]);

        $rowIds = array_filter(array_map('intval', explode(',', $request->row_ids)));
        if (empty($rowIds)) {
            return response()->json(['success' => false, 'message' => 'لم يتم العثور على سجلات المستفيد.'], 422);
        }

        $note = trim($request->input('note', ''));
        $tag = !empty($note) ? "[ملاحظة:{$note}]" : '';

        $rows = DB::table('project_beneficiaries')->whereIn('id', $rowIds)->get();
        foreach ($rows as $row) {
            $existing = $row->notes ?? '';
            if (preg_match('/\[ملاحظة:[^\]]+\]/u', $existing)) {
                $newNotes = preg_replace('/\[ملاحظة:[^\]]+\]/u', $tag, $existing);
            } else {
                $newNotes = trim($existing . ' ' . $tag);
            }
            DB::table('project_beneficiaries')->where('id', $row->id)->update([
                'notes' => trim($newNotes) ?: null,
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ ملاحظة المستفيد بنجاح.',
            'note' => $note ?: '-',
        ]);
    }
}
