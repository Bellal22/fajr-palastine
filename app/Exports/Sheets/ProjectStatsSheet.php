<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProjectStatsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected Project $project;
    protected array $filters;

    public function __construct(Project $project, array $filters)
    {
        $this->project = $project;
        $this->filters = $filters;
    }

    public function collection()
    {
        $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();

        $recipientsCount = DB::table('project_beneficiaries')
            ->where('project_id', $this->project->id)
            ->where('status', 'مستلم')
            ->whereBetween('delivery_date', [$dateFrom, $dateTo])
            ->count();

        return collect([[
            'المشروع / الكوبون' => $this->project->name,
            'تاريخ البداية (الفلتر)' => $dateFrom->format('Y-m-d'),
            'تاريخ النهاية (الفلتر)' => $dateTo->format('Y-m-d'),
            'عدد المستلمين في الفترة' => $recipientsCount
        ]]);
    }

    public function headings(): array
    {
        return [
            'المشروع / الكوبون',
            'تاريخ البداية (الفلتر)',
            'تاريخ النهاية (الفلتر)',
            'عدد المستلمين في الفترة',
        ];
    }

    public function title(): string
    {
        return 'إحصائيات المشروع';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $sheet->getStyle('A1:D1')->applyFromArray([
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
        
        $sheet->getStyle('A2:D2')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        return [];
    }
}
