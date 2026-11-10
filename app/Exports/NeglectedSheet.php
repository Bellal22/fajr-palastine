<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NeglectedSheet implements FromArray, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'فئات منسية (A/B)';
    }

    public function array(): array
    {
        $exportData = [];
        $i = 1;

        foreach ($this->data as $row) {
            $exportData[] = [
                $i++,
                $row->id_num ?? '',
                $row->name ?? 'غير معروف',
                $row->category ?? 'D',
                $row->score ?? 0,
                $row->last_delivery ?? 'لم يستلم أبداً',
                $row->reasons ?? ''
            ];
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            '#',
            'رقم الهوية',
            'الاسم',
            'الفئة',
            'النقاط',
            'آخر استلام',
            'السبب الرئيسي'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFC107'] // Warning color (yellow)
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        $sheet->getStyle('A1:G' . (count($this->data) + 1))->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        return [];
    }
}
