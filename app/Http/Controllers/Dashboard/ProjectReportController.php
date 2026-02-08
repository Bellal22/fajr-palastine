<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Project;
use App\Models\AreaResponsible;
use App\Models\SubWarehouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class ProjectReportController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        // 1. General Summary Stats (بداية الأسبوع من السبت)
        $stats = [
            'daily' => [
                'count' => Project::whereDate('created_at', $now->toDateString())->count(),
                'recipients' => $this->getRecipientsCountByPeriod('day', $now->copy()),
            ],
            'weekly' => [
                'count' => Project::whereBetween('created_at', [
                    $now->copy()->startOfWeek(Carbon::SATURDAY)->toDateTimeString(),
                    $now->copy()->endOfWeek(Carbon::FRIDAY)->toDateTimeString()
                ])->count(),
                'recipients' => $this->getRecipientsCountByPeriod('week', $now->copy()),
            ],
            'monthly' => [
                'count' => Project::whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)
                    ->count(),
                'recipients' => $this->getRecipientsCountByPeriod('month', $now->copy()),
            ],
            'yearly' => [
                'count' => Project::whereYear('created_at', $now->year)->count(),
                'recipients' => $this->getRecipientsCountByPeriod('year', $now->copy()),
            ],
        ];

        // 2. Detailed Project Reports
        $projects = Project::withCount([
            'beneficiaries as total_candidates',
            'beneficiaries as received_count' => function ($query) {
                $query->where('project_beneficiaries.status', 'مستلم');
            },
            'beneficiaries as not_received_count' => function ($query) {
                $query->where('project_beneficiaries.status', 'غير مستلم');
            }
        ])
            ->withSum(['beneficiaries as total_delivered_coupons' => function ($query) {
                $query->where('project_beneficiaries.status', 'مستلم');
            }], 'project_beneficiaries.quantity')
            ->with('couponTypes')
            ->latest()->get();

        // 3. Area-based Breakdown for Each Project
        $areaBreakdowns = DB::table('project_beneficiaries')
            ->join('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->leftJoin('persons as head', function ($join) {
                $join->on('persons.relative_id', '=', 'head.id_num')
                    ->whereNull('head.relative_id');
            })
            ->whereIn('project_beneficiaries.project_id', $projects->pluck('id'))
            ->where('project_beneficiaries.status', 'مستلم')
            ->select(
                'project_beneficiaries.project_id',
                DB::raw('COALESCE(persons.area_responsible_id, head.area_responsible_id) as area_id'),
                DB::raw('count(*) as count'),
                DB::raw('SUM(project_beneficiaries.quantity) as total_quantity')
            )
            ->groupBy('project_beneficiaries.project_id', 'area_id')
            ->get()
            ->groupBy('project_id');

        // 4. Sub-Warehouse Breakdown for Each Project
        $warehouseBreakdowns = DB::table('project_beneficiaries')
            ->whereIn('project_id', $projects->pluck('id'))
            ->where('status', 'مستلم')
            ->whereNotNull('sub_warehouse_id')
            ->select(
                'project_id',
                'sub_warehouse_id',
                DB::raw('count(*) as count'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('project_id', 'sub_warehouse_id')
            ->get()
            ->groupBy('project_id');

        $allAreaIds = DB::table('persons')->pluck('area_responsible_id')
            ->merge(DB::table('persons')
                ->whereNotNull('persons.relative_id')
                ->join('persons as heads', 'persons.relative_id', '=', 'heads.id_num')
                ->pluck('heads.area_responsible_id'))
            ->unique()
            ->filter();

        $areas = AreaResponsible::whereIn('id', $allAreaIds)->get()->keyBy('id');

        // Get all sub warehouses
        $allWarehouseIds = DB::table('project_beneficiaries')
            ->where('status', 'مستلم')
            ->whereNotNull('sub_warehouse_id')
            ->pluck('sub_warehouse_id')
            ->unique();
        $subWarehouses = SubWarehouse::whereIn('id', $allWarehouseIds)->get()->keyBy('id');

        // ✅ تحويل stdClass إلى arrays
        foreach ($projects as $project) {
            // Area breakdown - استخدم متغير مؤقت
            $areaData = $areaBreakdowns->get($project->id, collect())->keyBy('area_id');
            $tempAreaBreakdown = [];
            foreach ($areaData as $areaId => $data) {
                $tempAreaBreakdown[$areaId] = [
                    'count' => $data->count,
                    'total_quantity' => $data->total_quantity
                ];
            }
            $project->area_breakdown = $tempAreaBreakdown;

            // Warehouse breakdown - استخدم متغير مؤقت
            $warehouseData = $warehouseBreakdowns->get($project->id, collect())->keyBy('sub_warehouse_id');
            $tempWarehouseBreakdown = [];
            foreach ($warehouseData as $warehouseId => $data) {
                $tempWarehouseBreakdown[$warehouseId] = [
                    'count' => $data->count,
                    'total_quantity' => $data->total_quantity
                ];
            }
            $project->warehouse_breakdown = $tempWarehouseBreakdown;
        }

        return view('dashboard.reports.projects', compact('stats', 'projects', 'areas', 'subWarehouses'));
    }

    public function show(Project $project)
    {
        $project->loadCount([
            'beneficiaries as total_candidates',
            'beneficiaries as received_count' => function ($query) {
                $query->where('project_beneficiaries.status', 'مستلم');
            },
            'beneficiaries as not_received_count' => function ($query) {
                $query->where('project_beneficiaries.status', 'غير مستلم');
            }
        ]);

        // Get area breakdown for this specific project
        $areaBreakdownRaw = DB::table('project_beneficiaries')
            ->join('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->leftJoin('persons as head', function ($join) {
                $join->on('persons.relative_id', '=', 'head.id_num')
                    ->whereNull('head.relative_id');
            })
            ->where('project_beneficiaries.project_id', $project->id)
            ->where('project_beneficiaries.status', 'مستلم')
            ->select(
                DB::raw('COALESCE(persons.area_responsible_id, head.area_responsible_id) as area_id'),
                DB::raw('count(*) as count'),
                DB::raw('SUM(project_beneficiaries.quantity) as total_quantity')
            )
            ->groupBy('area_id')
            ->get()
            ->keyBy('area_id');

        // Get warehouse breakdown for this specific project
        $warehouseBreakdownRaw = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('status', 'مستلم')
            ->whereNotNull('sub_warehouse_id')
            ->select(
                'sub_warehouse_id',
                DB::raw('count(*) as count'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('sub_warehouse_id')
            ->get()
            ->keyBy('sub_warehouse_id');

        // ✅ تحويل stdClass إلى arrays - استخدم متغير مؤقت
        $tempAreaBreakdown = [];
        foreach ($areaBreakdownRaw as $areaId => $data) {
            $tempAreaBreakdown[$areaId] = [
                'count' => $data->count,
                'total_quantity' => $data->total_quantity
            ];
        }

        $tempWarehouseBreakdown = [];
        foreach ($warehouseBreakdownRaw as $warehouseId => $data) {
            $tempWarehouseBreakdown[$warehouseId] = [
                'count' => $data->count,
                'total_quantity' => $data->total_quantity
            ];
        }

        $project->area_breakdown = $tempAreaBreakdown;
        $project->warehouse_breakdown = $tempWarehouseBreakdown;

        // Load coupon types
        $project->load('couponTypes');

        // Calculate total delivered coupons (sum of quantities given to beneficiaries)
        $totalDeliveredCoupons = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('status', 'مستلم')
            ->sum('quantity');

        $areas = AreaResponsible::whereIn('id', array_keys($tempAreaBreakdown))->get()->keyBy('id');
        $subWarehouses = SubWarehouse::whereIn('id', array_keys($tempWarehouseBreakdown))->get()->keyBy('id');

        return view('dashboard.reports.project', compact('project', 'areas', 'subWarehouses', 'totalDeliveredCoupons'));
    }

    public function export(Request $request, Project $project)
    {
        // Reuse logic from show method to prepare data
        $project->loadCount([
            'beneficiaries as total_candidates',
            'beneficiaries as received_count' => function ($query) {
                $query->where('project_beneficiaries.status', 'مستلم');
            },
            'beneficiaries as not_received_count' => function ($query) {
                $query->where('project_beneficiaries.status', 'غير مستلم');
            }
        ]);

        $areaBreakdownRaw = DB::table('project_beneficiaries')
            ->join('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->leftJoin('persons as head', function ($join) {
                $join->on('persons.relative_id', '=', 'head.id_num')
                    ->whereNull('head.relative_id');
            })
            ->where('project_beneficiaries.project_id', $project->id)
            ->where('project_beneficiaries.status', 'مستلم')
            ->select(
                DB::raw('COALESCE(persons.area_responsible_id, head.area_responsible_id) as area_id'),
                DB::raw('count(*) as count'),
                DB::raw('SUM(project_beneficiaries.quantity) as total_quantity')
            )
            ->groupBy('area_id')
            ->get()
            ->keyBy('area_id');

        $warehouseBreakdownRaw = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('status', 'مستلم')
            ->whereNotNull('sub_warehouse_id')
            ->select(
                'sub_warehouse_id',
                DB::raw('count(*) as count'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('sub_warehouse_id')
            ->get()
            ->keyBy('sub_warehouse_id');

        // ✅ تحويل stdClass إلى arrays - استخدم متغير مؤقت
        $tempAreaBreakdown = [];
        foreach ($areaBreakdownRaw as $areaId => $data) {
            $tempAreaBreakdown[$areaId] = [
                'count' => $data->count,
                'total_quantity' => $data->total_quantity
            ];
        }

        $tempWarehouseBreakdown = [];
        foreach ($warehouseBreakdownRaw as $warehouseId => $data) {
            $tempWarehouseBreakdown[$warehouseId] = [
                'count' => $data->count,
                'total_quantity' => $data->total_quantity
            ];
        }

        $project->area_breakdown = $tempAreaBreakdown;
        $project->warehouse_breakdown = $tempWarehouseBreakdown;
        $project->load('couponTypes');

        $totalDeliveredCoupons = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('status', 'مستلم')
            ->sum('quantity');

        $areas = AreaResponsible::whereIn('id', array_keys($tempAreaBreakdown))->get()->keyBy('id');
        $subWarehouses = SubWarehouse::whereIn('id', array_keys($tempWarehouseBreakdown))->get()->keyBy('id');

        if ($request->type === 'pdf') {
            $html = view('dashboard.reports.project_pdf', [
                'project' => $project,
                'areas' => $areas,
                'subWarehouses' => $subWarehouses,
                'totalDeliveredCoupons' => $totalDeliveredCoupons,
            ])->render();

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'default_font' => 'dejavusans'
            ]);

            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;

            $mpdf->WriteHTML($html);

            return $mpdf->Output('project-report-' . $project->id . '.pdf', 'D');
        } elseif ($request->type === 'xlsx') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\ProjectReportExport($project, $project->area_breakdown, $areas),
                'project-report-' . $project->id . '.xlsx'
            );
        }

        abort(404);
    }

    public function periodReport(Request $request, $period)
    {
        $dateInput = $request->get('date');
        $today = $dateInput ? Carbon::parse($dateInput) : now();
        $startDate = null;
        $endDate = null;
        $label = '';

        switch ($period) {
            case 'daily':
                $startDate = $today->copy()->startOfDay();
                $endDate = $today->copy()->endOfDay();
                $label = 'اليومية';
                if ($dateInput) {
                    $label .= ' بتاريخ ' . $today->format('Y-m-d');
                }
                break;
            case 'weekly':
                // بداية الأسبوع من السبت
                $startDate = $today->copy()->startOfWeek(Carbon::SATURDAY);
                $endDate = $today->copy()->endOfWeek(Carbon::FRIDAY);
                $label = 'الأسبوعية';
                if ($dateInput) {
                    $label .= ' للأسبوع من ' . $startDate->format('Y-m-d') . ' إلى ' . $endDate->format('Y-m-d');
                }
                break;
            case 'monthly':
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfMonth();
                $label = 'الشهرية';
                if ($dateInput) {
                    $label .= ' لشهر ' . $today->translatedFormat('F Y');
                }
                break;
            case 'yearly':
                $startDate = $today->copy()->startOfYear();
                $endDate = $today->copy()->endOfYear();
                $label = 'السنوية';
                if ($dateInput) {
                    $label .= ' لعام ' . $today->format('Y');
                }
                break;
            default:
                abort(404);
        }

        // 1. Projects CREATED in this period
        $createdProjects = Project::whereBetween('created_at', [$startDate, $endDate])->get();

        // 2. Active Projects (Deliveries happened in this period)
        $activeProjectIds = DB::table('project_beneficiaries')
            ->where('status', 'مستلم')
            ->whereBetween('delivery_date', [$startDate, $endDate])
            ->pluck('project_id')
            ->unique();

        $activeProjects = Project::whereIn('id', $activeProjectIds)
            ->with('couponTypes')
            ->get();

        $deliveredItemsSummary = [];

        foreach ($activeProjects as $project) {
            // Calculate total quantity delivered in this period (Sum of quantities)
            $project->period_delivered_quantity = DB::table('project_beneficiaries')
                ->where('project_id', $project->id)
                ->where('status', 'مستلم')
                ->whereBetween('delivery_date', [$startDate, $endDate])
                ->sum('quantity');

            // Count beneficiaries as well for the table display
            $project->period_received_count = DB::table('project_beneficiaries')
                ->where('project_id', $project->id)
                ->where('status', 'مستلم')
                ->whereBetween('delivery_date', [$startDate, $endDate])
                ->count();

            // Accumulate Item Quantities
            foreach ($project->couponTypes as $type) {
                if (!isset($deliveredItemsSummary[$type->name])) {
                    $deliveredItemsSummary[$type->name] = 0;
                }
                // (Quantity of Type in Coupon) * (Sum of delivered coupons quantity in period)
                $deliveredItemsSummary[$type->name] += ($type->pivot->quantity * $project->period_delivered_quantity);
            }
        }

        // 3. Recipients list for this period
        $recipients = DB::table('project_beneficiaries')
            ->join('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->join('projects', 'project_beneficiaries.project_id', '=', 'projects.id')
            ->leftJoin('persons as head', function ($join) {
                $join->on('persons.relative_id', '=', 'head.id_num')
                    ->whereNull('head.relative_id');
            })
            ->where('project_beneficiaries.status', 'مستلم')
            ->whereBetween('project_beneficiaries.delivery_date', [$startDate, $endDate])
            ->select(
                DB::raw('CONCAT_WS(" ", persons.first_name, persons.father_name, persons.grandfather_name, persons.family_name) as person_name'),
                'projects.name as project_name',
                'project_beneficiaries.delivery_date',
                DB::raw('COALESCE(persons.area_responsible_id, head.area_responsible_id) as area_id')
            )
            ->orderBy('project_beneficiaries.delivery_date', 'desc')
            ->paginate(50);

        // 4. Area breakdown for this period
        $areaBreakdownRaw = DB::table('project_beneficiaries')
            ->join('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->leftJoin('persons as head', function ($join) {
                $join->on('persons.relative_id', '=', 'head.id_num')
                    ->whereNull('head.relative_id');
            })
            ->where('project_beneficiaries.status', 'مستلم')
            ->whereBetween('project_beneficiaries.delivery_date', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(persons.area_responsible_id, head.area_responsible_id) as area_id'),
                DB::raw('count(*) as count'),
                DB::raw('SUM(project_beneficiaries.quantity) as total_quantity')
            )
            ->groupBy('area_id')
            ->get()
            ->keyBy('area_id');

        // 5. Warehouse breakdown for this period
        $warehouseBreakdownRaw = DB::table('project_beneficiaries')
            ->where('status', 'مستلم')
            ->whereBetween('delivery_date', [$startDate, $endDate])
            ->whereNotNull('sub_warehouse_id')
            ->select(
                'sub_warehouse_id',
                DB::raw('count(*) as count'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('sub_warehouse_id')
            ->get()
            ->keyBy('sub_warehouse_id');

        // ✅ تحويل stdClass إلى arrays
        $areaBreakdown = [];
        foreach ($areaBreakdownRaw as $areaId => $data) {
            $areaBreakdown[$areaId] = [
                'count' => $data->count,
                'total_quantity' => $data->total_quantity
            ];
        }

        $warehouseBreakdown = [];
        foreach ($warehouseBreakdownRaw as $warehouseId => $data) {
            $warehouseBreakdown[$warehouseId] = [
                'count' => $data->count,
                'total_quantity' => $data->total_quantity
            ];
        }

        $areas = AreaResponsible::whereIn('id', array_keys($areaBreakdown))->get()->keyBy('id');
        $subWarehouses = SubWarehouse::whereIn('id', array_keys($warehouseBreakdown))->get()->keyBy('id');

        return view('dashboard.reports.period', compact('createdProjects', 'activeProjects', 'recipients', 'areaBreakdown', 'warehouseBreakdown', 'areas', 'subWarehouses', 'label', 'period', 'today', 'deliveredItemsSummary'));
    }

    private function getRecipientsCountByPeriod($period, $date)
    {
        $query = DB::table('project_beneficiaries')
            ->where('status', 'مستلم');

        switch ($period) {
            case 'day':
                $query->whereDate('delivery_date', $date->toDateString());
                break;
            case 'week':
                // بداية الأسبوع من السبت
                $startOfWeek = $date->copy()->startOfWeek(Carbon::SATURDAY);
                $endOfWeek = $date->copy()->endOfWeek(Carbon::FRIDAY);

                $query->whereBetween('delivery_date', [
                    $startOfWeek->toDateString(),
                    $endOfWeek->toDateString()
                ]);
                break;
            case 'month':
                $query->whereMonth('delivery_date', $date->month)
                    ->whereYear('delivery_date', $date->year);
                break;
            case 'year':
                $query->whereYear('delivery_date', $date->year);
                break;
        }

        return $query->count();
    }
}
