<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class IncompleteSkippedExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'السطر في الملف',
            'رقم هوية الزوج',
            'اسم الزوج',
            'رقم الجوال المدخل',
            'رقم هوية الزوجة',
            'اسم الزوجة',
            'أسباب الاستبعاد (المشاكل المكتشفة)'
        ];
    }

    public function array(): array
    {
        return array_map(function ($item) {
            $reasonsText = is_array($item['reasons'] ?? null)
                ? implode(' | ', $item['reasons'])
                : ($item['reasons'] ?? '-');

            return [
                $item['row'] ?? '-',
                $item['husband_id'] ?? '-',
                $item['husband_name'] ?? '-',
                $item['phone'] ?? '-',
                $item['wife_id'] ?? '-',
                $item['wife_name'] ?? '-',
                $reasonsText,
            ];
        }, $this->data);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);

                // Styling header row
                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'C0392B'], // Red header
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
