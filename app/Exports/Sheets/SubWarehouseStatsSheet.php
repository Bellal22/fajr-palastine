<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubWarehouseStatsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, ShouldQueue
{
    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();

        // Get projects that have distributions in this period
        $activeProjectIds = DB::table('project_beneficiaries')
            ->where('status', 'مستلم')
            ->whereBetween('delivery_date', [$dateFrom, $dateTo]);

        if (isset($this->filters['sub_warehouse_id']) && $this->filters['sub_warehouse_id']) {
            $activeProjectIds->where('sub_warehouse_id', $this->filters['sub_warehouse_id']);
        }

        $activeProjectIds = $activeProjectIds->pluck('project_id')->unique();

        if ($activeProjectIds->isEmpty()) {
            return new Collection([[]]);
        }

        $projects = \App\Models\Project::whereIn('id', $activeProjectIds)->orderBy('id')->get();
        $results = [];

        foreach ($projects as $project) {
            $query = DB::table('project_beneficiaries')
                ->where('project_id', $project->id)
                ->where('status', 'مستلم')
                ->whereBetween('delivery_date', [$dateFrom, $dateTo]);

            if (isset($this->filters['sub_warehouse_id']) && $this->filters['sub_warehouse_id']) {
                $query->where('sub_warehouse_id', $this->filters['sub_warehouse_id']);
            }

            $count = $query->sum('quantity');
            $results[$project->id] = $count ?: 0;
        }

        return new Collection([$results]);
    }

    public function headings(): array
    {
        $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();

        $activeProjectIds = DB::table('project_beneficiaries')
            ->where('status', 'مستلم')
            ->whereBetween('delivery_date', [$dateFrom, $dateTo]);

        if (isset($this->filters['sub_warehouse_id']) && $this->filters['sub_warehouse_id']) {
            $activeProjectIds->where('sub_warehouse_id', $this->filters['sub_warehouse_id']);
        }

        $activeProjectIds = $activeProjectIds->pluck('project_id')->unique();

        if ($activeProjectIds->isEmpty()) {
            return ['لا توجد بيانات'];
        }

        return \App\Models\Project::whereIn('id', $activeProjectIds)->orderBy('id')->pluck('name')->toArray();
    }

    public function title(): string
    {
        return 'إحصائيات عامة';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $highestColumn = $sheet->getHighestColumn();
        $range = 'A1:' . $highestColumn . '1';

        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $dataRange = 'A2:' . $highestColumn . $highestRow;
            $sheet->getStyle($dataRange)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ]);
        }

        return [];
    }
}
