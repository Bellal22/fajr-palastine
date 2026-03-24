<?php

namespace App\Exports;

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 0);
ini_set('max_input_time', -1);

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Contracts\Queue\ShouldQueue;

class BeneficiariesByDateExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable;

    protected array $filters;
    protected ?Project $project;

    public function __construct(array $filters, ?Project $project = null)
    {
        $this->filters = $filters;
        $this->project = $project;
    }

    public function sheets(): array
    {
        $sheets = [];

        if ($this->project) {
            // حالة اختيار مشروع محدد
            // الصفحة الأولى: إحصائيات المشروع
            $sheets[] = new \App\Exports\Sheets\ProjectStatsSheet($this->project, $this->filters);
            // الصفحة الثانية: كل المستلمين للمشروع
            $sheets[] = new \App\Exports\Sheets\ProjectBeneficiariesQuerySheet($this->project, $this->filters);
            
        } else {
            // حالة عدم تحديد مشروع: نجلب كل المشاريع النشطة في هذه الفترة
            $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
            $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();

            $activeProjectIds = DB::table('project_beneficiaries')
                ->where('status', 'مستلم')
                ->whereBetween('delivery_date', [$dateFrom, $dateTo])
                ->pluck('project_id')
                ->unique();

            $activeProjects = Project::whereIn('id', $activeProjectIds)->get();

            // الصفحة الأولى: ملخص عام للمشاريع وعدد المستلمين
            $sheets[] = new \App\Exports\Sheets\OverallProjectsSummarySheet($activeProjects, $this->filters);

            // صفحات أخرى: صفحة لكل مشروع مدمج فيها الإحصائيات مع البيانات
            foreach ($activeProjects as $activeProject) {
                $sheets[] = new \App\Exports\Sheets\ProjectBeneficiariesQuerySheet($activeProject, $this->filters, true);
            }

            if ($activeProjects->count() == 0) {
                 $sheets[] = new \App\Exports\Sheets\EmptyTemplateSheet();
            }
        }

        return $sheets;
    }
}
