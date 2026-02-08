<?php

namespace App\Exports;

use App\Models\Block;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;

class BlockPersonsSheet implements FromQuery, WithHeadings, WithTitle, WithStyles, WithMapping
{
    protected $block;
    
    public function __construct(Block $block)
    {
        $this->block = $block;
    }

    public function query()
    {
        return $this->block->people()
            ->familyHead() // Filter: Family Head only
            ->approved()   // Filter: Approved only
            ->where('area_responsible_id', $this->block->area_responsible_id) // Strict Check: Must match Block's Area Responsible
            ->with(['familyMembers' => function ($query) {
                // Eager load only wives to optimize
                $query->where('relationship', 'زوجة');
            }])
            ->latest();
    }

    public function map($person): array
    {
        // تركيب الاسم الرباعي
        $fullName = trim($person->first_name . ' ' . $person->father_name . ' ' . $person->grandfather_name . ' ' . $person->family_name);
        
        // حساب عدد الأفراد
        $familyCount = $person->relatives_count ?? ($person->relatives()->count() + 1);

        // تجهيز بيانات الزوجات في نص واحد
        $wivesData = $person->familyMembers
            ->where('relationship', 'زوجة')
            ->map(function ($wife) {
                $wifeName = trim($wife->first_name . ' ' . $wife->father_name . ' ' . $wife->grandfather_name . ' ' . $wife->family_name);
                return "[{$wife->id_num}] {$wifeName}";
            })
            ->implode(' - ');

        return [
            $person->id_num,
            $fullName,
            $person->phone,
            $familyCount,
            $person->social_status,
            $wivesData, // بيانات الزوجات (عمود واحد)
            $person->notes ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'رقم الهوية',
            'الاسم رباعي',
            'رقم الجوال',
            'عدد الأفراد',
            'الحالة الاجتماعية',
            'الزوجات (رقم الهوية - الاسم)',
            'ملاحظات',
        ];
    }

    public function title(): string
    {
        return mb_substr($this->block->name, 0, 30);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        // Header Styling from PeopleExport (Orange)
        $sheet->getStyle('A1:G1')->applyFromArray([
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
}
