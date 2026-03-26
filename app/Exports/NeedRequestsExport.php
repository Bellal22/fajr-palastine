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

class NeedRequestsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function map($item): array
    {
        static $i = 0;
        $i++;
        
        $person = $item->person;
        $needRequest = $item->needRequest;

        return [
            $i,
            $person->id_num ?? '-',
            $person->quad_name ?? '-',
            $person->phone ?? '-',
            $person->areaResponsible->name ?? '-',
            $person->block->name ?? '-',
            $needRequest->id ?? '-',
            $needRequest->project->name ?? '-',
            trans('need_requests.statuses.' . $item->status),
        ];
    }

    public function headings(): array
    {
        return [
            'م',
            'رقم الهوية',
            'الاسم الرباعي',
            'رقم الهاتف',
            'المنطقة',
            'المندوب',
            'رقم طلب الاحتياج',
            'المشروع',
            'الحالة',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        
        $sheet->getStyle('A1:I1')->applyFromArray([
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
