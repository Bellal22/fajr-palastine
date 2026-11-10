<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CustomProject;
use App\Models\Project;
use App\Models\RrmBatch;
use App\Models\SubWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomProjectController extends Controller
{
    /**
     * Check permission to manage custom projects.
     */
    protected function checkPermission(): void
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
                return;
            }
        }
        abort(403, 'غير مصرح لك بالوصول إلى إدارة المشاريع المخصصة');
    }

    /**
     * Display a listing of all Custom Projects (including RRM).
     */
    public function index()
    {
        $this->checkPermission();
        CustomProject::ensureTableExists();

        $customProjects = CustomProject::with('defaultSubWarehouse')
            ->withCount('batches')
            ->orderByRaw("CASE WHEN slug = 'rrm' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->get();

        // Compute aggregate stats per custom project
        $batchStats = RrmBatch::selectRaw('custom_project_id, COUNT(*) as batches_count, COALESCE(SUM(beneficiaries_count), 0) as total_beneficiaries, COALESCE(SUM(total_parcels), 0) as total_parcels')
            ->groupBy('custom_project_id')
            ->get()
            ->keyBy('custom_project_id');

        $projectsMap = Project::pluck('name', 'id')->toArray();

        return view('dashboard.custom_projects.index', compact('customProjects', 'batchStats', 'projectsMap'));
    }

    /**
     * Show the form for creating a new Custom Project.
     */
    public function create()
    {
        $this->checkPermission();
        CustomProject::ensureTableExists();

        $projects = Project::orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->get();
        $warehouses = SubWarehouse::orderBy('name')->get();

        return view('dashboard.custom_projects.form', [
            'customProject' => new CustomProject([
                'code_prefix' => 'PRJ',
                'icon' => 'fas fa-boxes',
                'color' => 'primary',
                'coupon_scope' => 'multiple',
                'auto_add_if_missing' => true,
                'skip_already_delivered' => true,
                'is_active' => true,
            ]),
            'projects' => $projects,
            'warehouses' => $warehouses,
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created Custom Project in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission();
        CustomProject::ensureTableExists();

        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'code_prefix' => 'required|string|max:20',
            'icon' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:1000',
            'is_cash' => 'nullable|boolean',
            'cash_amount' => 'nullable|required_if:is_cash,1|numeric|min:0.01',
            'cash_currency' => 'nullable|required_if:is_cash,1|string|in:ILS,USD,JOD,EUR',
            'coupon_scope' => 'required|in:single,multiple,both',
            'default_project_ids' => 'nullable|array',
            'default_project_ids.*' => 'exists:projects,id',
            'default_project_quantities' => 'nullable|array',
            'default_sub_warehouse_id' => 'nullable|exists:sub_warehouses,id',
        ], [
            'cash_amount.required_if' => 'يرجى إدخال قيمة المبلغ للمشروع المخصص النقدي',
            'cash_amount.numeric' => 'قيمة المبلغ يجب أن تكون رقماً',
            'cash_amount.min' => 'قيمة المبلغ يجب أن تكون أكبر من صفر',
            'cash_currency.required_if' => 'يرجى اختيار عملة المشروع المخصص النقدي',
        ]);

        $slugBase = Str::slug($validated['code_prefix']) ?: Str::slug($validated['name']) ?: 'custom-project';
        $slug = $slugBase;
        $counter = 1;
        while (CustomProject::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        $defaultIds = array_map('intval', $request->input('default_project_ids', []));
        $rawQuantities = $request->input('default_project_quantities', []);
        $cleanQuantities = [];
        foreach ($defaultIds as $pid) {
            $cleanQuantities[$pid] = max(1, (int) ($rawQuantities[$pid] ?? 1));
        }

        $isCash = $request->boolean('is_cash');

        $customProject = CustomProject::create([
            'name' => trim($validated['name']),
            'slug' => $slug,
            'code_prefix' => strtoupper(preg_replace('/[^A-Za-z0-9_-]/', '', $validated['code_prefix']) ?: 'PRJ'),
            'icon' => $validated['icon'] ?: 'fas fa-boxes',
            'color' => $validated['color'] ?: 'primary',
            'description' => $validated['description'] ?? null,
            'is_cash' => $isCash,
            'cash_amount' => $isCash ? $validated['cash_amount'] : null,
            'cash_currency' => $isCash ? $validated['cash_currency'] : null,
            'coupon_scope' => $validated['coupon_scope'],
            'default_project_ids' => $defaultIds,
            'default_project_quantities' => $cleanQuantities,
            'default_sub_warehouse_id' => !empty($validated['default_sub_warehouse_id']) ? (int) $validated['default_sub_warehouse_id'] : null,
            'auto_add_if_missing' => $request->boolean('auto_add_if_missing'),
            'skip_already_delivered' => $request->boolean('skip_already_delivered'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        flash()->success("تم إنشاء المشروع المخصص ({$customProject->name}) وتخصيص طريقة التعامل معه بنجاح.");

        return redirect()->route('dashboard.custom_projects.index');
    }

    /**
     * Show the form for editing an existing Custom Project.
     */
    public function edit(CustomProject $custom_project)
    {
        $this->checkPermission();
        CustomProject::ensureTableExists();

        $projects = Project::orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('id', 'desc')
            ->get();
        $warehouses = SubWarehouse::orderBy('name')->get();

        return view('dashboard.custom_projects.form', [
            'customProject' => $custom_project,
            'projects' => $projects,
            'warehouses' => $warehouses,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified Custom Project in storage.
     */
    public function update(Request $request, CustomProject $custom_project)
    {
        $this->checkPermission();
        CustomProject::ensureTableExists();

        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'code_prefix' => 'required|string|max:20',
            'icon' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:1000',
            'is_cash' => 'nullable|boolean',
            'cash_amount' => 'nullable|required_if:is_cash,1|numeric|min:0.01',
            'cash_currency' => 'nullable|required_if:is_cash,1|string|in:ILS,USD,JOD,EUR',
            'coupon_scope' => 'required|in:single,multiple,both',
            'default_project_ids' => 'nullable|array',
            'default_project_ids.*' => 'exists:projects,id',
            'default_project_quantities' => 'nullable|array',
            'default_sub_warehouse_id' => 'nullable|exists:sub_warehouses,id',
        ], [
            'cash_amount.required_if' => 'يرجى إدخال قيمة المبلغ للمشروع المخصص النقدي',
            'cash_amount.numeric' => 'قيمة المبلغ يجب أن تكون رقماً',
            'cash_amount.min' => 'قيمة المبلغ يجب أن تكون أكبر من صفر',
            'cash_currency.required_if' => 'يرجى اختيار عملة المشروع المخصص النقدي',
        ]);

        $defaultIds = array_map('intval', $request->input('default_project_ids', []));
        $rawQuantities = $request->input('default_project_quantities', []);
        $cleanQuantities = [];
        foreach ($defaultIds as $pid) {
            $cleanQuantities[$pid] = max(1, (int) ($rawQuantities[$pid] ?? 1));
        }

        $isCash = $request->boolean('is_cash');

        $custom_project->update([
            'name' => trim($validated['name']),
            'code_prefix' => strtoupper(preg_replace('/[^A-Za-z0-9_-]/', '', $validated['code_prefix']) ?: $custom_project->code_prefix),
            'icon' => $validated['icon'] ?: 'fas fa-boxes',
            'color' => $validated['color'] ?: 'primary',
            'description' => $validated['description'] ?? null,
            'is_cash' => $isCash,
            'cash_amount' => $isCash ? $validated['cash_amount'] : null,
            'cash_currency' => $isCash ? $validated['cash_currency'] : null,
            'coupon_scope' => $validated['coupon_scope'],
            'default_project_ids' => $defaultIds,
            'default_project_quantities' => $cleanQuantities,
            'default_sub_warehouse_id' => !empty($validated['default_sub_warehouse_id']) ? (int) $validated['default_sub_warehouse_id'] : null,
            'auto_add_if_missing' => $request->boolean('auto_add_if_missing'),
            'skip_already_delivered' => $request->boolean('skip_already_delivered'),
            'is_active' => $request->boolean('is_active'),
        ]);

        flash()->success("تم تحديث إعدادات وطريقة التعامل مع المشروع المخصص ({$custom_project->name}) بنجاح.");

        return redirect()->route('dashboard.custom_projects.index');
    }

    /**
     * Toggle active status of a Custom Project.
     */
    public function toggleStatus(CustomProject $custom_project)
    {
        $this->checkPermission();
        $custom_project->update(['is_active' => !$custom_project->is_active]);

        flash()->success($custom_project->is_active ? "تم تفعيل ({$custom_project->name}) بنجاح." : "تم إيقاف ({$custom_project->name}) مؤقتاً.");
        return back();
    }

    /**
     * Remove the specified Custom Project from storage.
     */
    public function destroy(CustomProject $custom_project)
    {
        $this->checkPermission();

        if ($custom_project->slug === 'rrm') {
            return back()->withErrors(['error' => 'مشروع الـ RRM هو مشروع أساسي في النظام، يمكنك تعديل إعداداته أو إيقافه بدلاً من حذفه.']);
        }

        $batchesCount = $custom_project->batches()->count();
        if ($batchesCount > 0) {
            return back()->withErrors(['error' => "لا يمكن حذف هذا المشروع المخصص لأنه مرتبط بـ ({$batchesCount}) كشف توزيع. يمكنك إيقاف تفعيله بدلاً من ذلك."]);
        }

        $name = $custom_project->name;
        $custom_project->delete();

        flash()->success("تم حذف المشروع المخصص ({$name}) بنجاح.");
        return redirect()->route('dashboard.custom_projects.index');
    }
}

