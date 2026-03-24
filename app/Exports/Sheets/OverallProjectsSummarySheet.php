<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OverallProjectsSummarySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, ShouldQueue
{
    protected Collection $projects;
    protected array $filters;

    public function __construct(Collection $projects, array $filters)
    {
        $this->projects = $projects;
        $this->filters = $filters;
    }

    public function collection()
    {
        $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();

        $data = [];
        $totalOverall = 0;

        foreach ($this->projects as $project) {
            $recipientsCount = DB::table('project_beneficiaries')
                ->where('project_id', $project->id)
                ->where('status', 'مستلم')
                ->whereBetween('delivery_date', [$dateFrom, $dateTo])
                ->count();
            
            $totalOverall += $recipientsCount;

            $data[] = [
                'المشروع / الكوبون' => $project->name,
                'تاريخ البداية (الفلتر)' => $dateFrom->format('Y-m-d'),
                'تاريخ النهاية (الفلتر)' => $dateTo->format('Y-m-d'),
                'عدد المستلمين في الفترة' => $recipientsCount
            ];
        }

        // إجمالي لكل المشاريع
        $data[] = [
            'المشروع / الكوبون' => 'المجموع الكلي',
            'تاريخ البداية (الفلتر)' => '',
            'تاريخ النهاية (الفلتر)' => '',
            'عدد المستلمين في الفترة' => $totalOverall
        ];

        return collect($data);
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
        return 'إحصائيات المشاريع - العام';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $highestRow = $sheet->getHighestRow();
        
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500'] // برتقالي كما طلب التنسيق
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);
        
        $sheet->getStyle('A2:D' . $highestRow)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        // تنسيق صف المجموع الكلي (آخر صف)
        $sheet->getStyle('A' . $highestRow . ':D' . $highestRow)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'] 
            ],
        ]);

        return [];
    }
}
