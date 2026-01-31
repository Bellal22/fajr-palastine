<?php

namespace App\Exports;

use App\Models\NeedRequest;
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

class NeedRequestItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $needRequest;

    public function __construct(NeedRequest $needRequest)
    {
        $this->needRequest = $needRequest;
    }

    public function collection()
    {
        return $this->needRequest->items()->with('person.areaResponsible', 'person.block')->get();
    }

    public function map($item): array
    {
        $person = $item->person;
        return [
            $person->id_num ?? '-',
            $person->quad_name ?? '-',
            $person->phone ?? '-',
            $person->areaResponsible->name ?? '-',
            $person->block->name ?? '-',
            trans('need_requests.statuses.' . $item->status),
        ];
    }

    public function headings(): array
    {
        return [
            'رقم الهوية',
            'الاسم رباعي',
            'رقم الهاتف',
            'المنطقة',
            'المربع (المندوب)',
            'حالة البند',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        
        $sheet->getStyle('A1:F1')->applyFromArray([
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
        return 'كشف الأشخاص في طلب الاحتياج';
    }
}
