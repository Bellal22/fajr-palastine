<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProjectSummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $data;
    protected $areas;

    public function __construct($breakdownData, $areas)
    {
        $this->data = $breakdownData;
        $this->areas = $areas;
    }

    public function collection()
    {
         $rows = [];
        foreach ($this->data as $areaId => $stats) {
            $areaName = $this->areas[$areaId]->name ?? 'غير محدد';
            $rows[] = [
                'area' => $areaName,
                'count' => $stats['count'] ?? 0,
                'quantity' => $stats['total_quantity'] ?? 0,
            ];
        }
        return collect($rows);
    }

    public function map($row): array
    {
        return [
            $row['area'],
            $row['count'],
            $row['quantity'],
        ];
    }

    public function headings(): array
    {
        return [
            'المنطقة',
            'عدد المستلمين',
            'إجمالي الكميات الموزعة',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        
        $sheet->getStyle('A1:C1')->applyFromArray([
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
        return [];
    }
    
    public function title(): string
    {
        return 'إحصائية التوزيع'; // Keeping the title
    }
}
