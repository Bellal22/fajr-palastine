<?php

namespace App\Exports;

use App\Models\AreaResponsible;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AreaResponsibleStatsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $areaResponsible;

    public function __construct(AreaResponsible $areaResponsible)
    {
        $this->areaResponsible = $areaResponsible;
    }

    public function collection()
    {
        // 1. Get all blocks belonging to this Area Responsible
        $blocks = $this->areaResponsible->blocks;
        $data = [];
        $i = 1;
        $totalApproved = 0;
        $grandTotalIndividuals = 0;

        foreach ($blocks as $block) {
            // 2. Count approved family heads:
            // - familyHead(): whereNull('relative_id')
            // - approved(): whereNotNull('area_responsible_id') AND whereNotNull('block_id')
            $headsQuery = $block->people()
                ->familyHead()
                ->approved()
                ->where('area_responsible_id', $this->areaResponsible->id); // Strict

            // 1. Approved Families Count
            $approvedCount = $headsQuery->count();

            // 2. Individuals Count (Heads + Relatives)
            // Use logic matching BlockPersonsSheet: 
            // If relatives_count > 0, use it. 
            // Else count actual relatives + 1 (Head).
            $totalIndividuals = $headsQuery->sum(
                \Illuminate\Support\Facades\DB::raw('COALESCE(NULLIF(relatives_count, 0), (SELECT COUNT(*) FROM persons AS p2 WHERE p2.relative_id = persons.id_num) + 1)')
            );

            $locationLink = ($block->lat && $block->lan) 
                ? "https://www.google.com/maps?q={$block->lat},{$block->lan}" 
                : '';

            $data[] = [
                'index' => $i++,
                'block_name' => $block->name,
                'location' => $locationLink,
                'count' => $approvedCount,
                'individuals_count' => $totalIndividuals // New Column
            ];
            
            $totalApproved += $approvedCount;
            $grandTotalIndividuals += $totalIndividuals;
        }

        // Return collection
        $collection = collect($data);
        
        // Add Total Row
        $collection->push([
            'index' => '',
            'block_name' => 'المجموع الكلي',
            'location' => '',
            'count' => $totalApproved,
            'individuals_count' => $grandTotalIndividuals
        ]);

        return $collection;
    }

    public function headings(): array
    {
        return [
            '#',
            'اسم المندوب',
            'الموقع (Location)',
            'عدد الأسر المعتمدة',
            'عدد الأفراد الكلي',
        ];
    }

    public function title(): string
    {
        return 'إحصائيات';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        // Header Styling (Same as PeopleExport - Orange)
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        // General Styling for all cells
        $sheet->getStyle('A:E')->applyFromArray([
            'font' => ['name' => 'Calibri', 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Manual Column Sizing (Since AutoSize is disabled)
        $sheet->getColumnDimension('A')->setWidth(10); // #
        $sheet->getColumnDimension('B')->setWidth(30); // Name
        $sheet->getColumnDimension('C')->setWidth(50); // Location
        $sheet->getColumnDimension('D')->setWidth(20); // Count families
        $sheet->getColumnDimension('E')->setWidth(20); // Count individuals

        // Total Row Styling (Last Row)
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A{$lastRow}:E{$lastRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFC107'] // Amber for total
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_DOUBLE]
            ]
        ]);
    }
}
