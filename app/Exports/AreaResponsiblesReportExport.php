<?php

namespace App\Exports;

use App\Models\AreaResponsible;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AreaResponsiblesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        $areaResponsibles = AreaResponsible::with(['blocks'])->get();

        foreach ($areaResponsibles as $area) {
            foreach ($area->blocks as $block) {
                // Count families
                $block->families_count = $block->people()
                    ->whereNull('relative_id')
                    ->where('area_responsible_id', $area->id)
                    ->count();

                // Count individuals
                $block->individuals_count = $block->people()
                    ->whereNull('relative_id')
                    ->where('area_responsible_id', $area->id)
                    ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(relatives_count, 0), (SELECT COUNT(*) FROM persons AS p2 WHERE p2.relative_id = persons.id_num) + 1)'));
            }
        }

        return $areaResponsibles;
    }

    public function map($areaResponsible): array
    {
        $rows = [];

        // If no blocks, add one row for the area responsible
        if ($areaResponsible->blocks->isEmpty()) {
            $rows[] = [
                $areaResponsible->name,
                $areaResponsible->address ?? '-',
                $areaResponsible->phone ?? '-',
                'لا يوجد مناديب',
                0,
                0,
                0, // Total Individuals for Rep (0 here)
                '-'
            ];
        } else {
            // Add a row for each block
            foreach ($areaResponsible->blocks as $block) {
                $rows[] = [
                    $areaResponsible->name,
                    $areaResponsible->address ?? '-',
                    $areaResponsible->phone ?? '-',
                    $block->name,
                    $block->families_count,
                    $block->individuals_count,
                    $areaResponsible->blocks->sum('individuals_count'), // Total for Rep
                    ($block->lat && $block->lan) ? "https://maps.google.com/?q={$block->lat},{$block->lan}" : '-'
                ];
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'مسؤول المنطقة',
            'العنوان',
            'رقم الهاتف',
            'المندوب (البلوك)',
            'عدد الأسر (للمندوب)',
            'عدد الأفراد (للمندوب)',
            'إجمالي الأفراد (لمسؤول المنطقة)',
            'موقع المندوب'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        // Header Styling from PeopleExport (Orange)
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
            ]
        ]);

        // General Styling for all columns
        $sheet->getStyle('A:H')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'font' => ['name' => 'Calibri', 'size' => 11],
        ]);
    }
}
