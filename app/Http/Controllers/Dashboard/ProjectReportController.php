<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Project;
use App\Models\AreaResponsible;
use App\Models\SubWarehouse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectReportController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        // 1. General Summary Stats (FIXED - بداية الأسبوع من السبت)
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
        ])->latest()->get();

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
                DB::raw('count(*) as count')
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
                DB::raw('count(*) as count')
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

        foreach ($projects as $project) {
            $project->area_breakdown = $areaBreakdowns->get($project->id, collect())->pluck('count', 'area_id')->toArray();
            $project->warehouse_breakdown = $warehouseBreakdowns->get($project->id, collect())->pluck('count', 'sub_warehouse_id')->toArray();
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
        $areaBreakdown = DB::table('project_beneficiaries')
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

        // Get warehouse breakdown for this specific project
        $warehouseBreakdown = DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->where('status', 'مستلم')
            ->whereNotNull('sub_warehouse_id')
            ->select(
                'sub_warehouse_id',
                DB::raw('count(*) as count')
            )
            ->groupBy('sub_warehouse_id')
            ->pluck('count', 'sub_warehouse_id')
            ->toArray();

        $project->area_breakdown = $areaBreakdown;
        $project->warehouse_breakdown = $warehouseBreakdown;

        $areas = AreaResponsible::whereIn('id', array_keys($areaBreakdown))->get()->keyBy('id');
        $subWarehouses = SubWarehouse::whereIn('id', array_keys($warehouseBreakdown))->get()->keyBy('id');

        return view('dashboard.reports.project', compact('project', 'areas', 'subWarehouses'));
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
                // ✅ بداية الأسبوع من السبت
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

        $activeProjects = Project::whereIn('id', $activeProjectIds)->get();

        foreach ($activeProjects as $project) {
            $project->period_received_count = DB::table('project_beneficiaries')
                ->where('project_id', $project->id)
                ->where('status', 'مستلم')
                ->whereBetween('delivery_date', [$startDate, $endDate])
                ->count();
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
        $areaBreakdown = DB::table('project_beneficiaries')
            ->join('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
            ->leftJoin('persons as head', function ($join) {
                $join->on('persons.relative_id', '=', 'head.id_num')
                    ->whereNull('head.relative_id');
            })
            ->where('project_beneficiaries.status', 'مستلم')
            ->whereBetween('project_beneficiaries.delivery_date', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(persons.area_responsible_id, head.area_responsible_id) as area_id'),
                DB::raw('count(*) as count')
            )
            ->groupBy('area_id')
            ->pluck('count', 'area_id')
            ->toArray();

        // 5. Warehouse breakdown for this period
        $warehouseBreakdown = DB::table('project_beneficiaries')
            ->where('status', 'مستلم')
            ->whereBetween('delivery_date', [$startDate, $endDate])
            ->whereNotNull('sub_warehouse_id')
            ->select(
                'sub_warehouse_id',
                DB::raw('count(*) as count')
            )
            ->groupBy('sub_warehouse_id')
            ->pluck('count', 'sub_warehouse_id')
            ->toArray();

        $areas = AreaResponsible::whereIn('id', array_keys($areaBreakdown))->get()->keyBy('id');
        $subWarehouses = SubWarehouse::whereIn('id', array_keys($warehouseBreakdown))->get()->keyBy('id');

        return view('dashboard.reports.period', compact('createdProjects', 'activeProjects', 'recipients', 'areaBreakdown', 'warehouseBreakdown', 'areas', 'subWarehouses', 'label', 'period', 'today'));
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
                // ✅ بداية الأسبوع من السبت
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
