<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $exportData = [];
        $i = 1;

        foreach ($this->data as $row) {
            $exportData[] = [
                $i++,
                $row->id_num,
                $row->full_name,
                $row->phone,
                $row->members_count,
                $row->reasons,
                $row->category,
                $row->score,
                $row->address
            ];
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            '#',
            'رقم الهوية',
            'الاسم الرباعي',
            'رقم الجوال',
            'عدد الأفراد',
            'تحليل الحالة (الزوجة/الأسباب/الأعمار)',
            'الفئة',
            'النقاط',
            'العنوان'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500'] // نفس اللون البرتقالي في PeopleExport
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        $sheet->getStyle('A1:I' . (count($this->data) + 1))->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        return [];
    }
}
