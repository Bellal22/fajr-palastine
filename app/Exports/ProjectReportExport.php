<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProjectReportExport implements WithMultipleSheets
{
    protected $project;
    protected $summaryData;
    protected $areas;

    public function __construct(Project $project, $summaryData, $areas)
    {
        $this->project = $project;
        $this->summaryData = $summaryData;
        $this->areas = $areas;
    }

    public function sheets(): array
    {
        return [
            new ProjectSummaryExport($this->summaryData, $this->areas),
            new ProjectBeneficiariesExport($this->project),
        ];
    }
}
