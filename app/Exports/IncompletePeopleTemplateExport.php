<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class IncompletePeopleTemplateExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'رقم هوية الزوج',
            'اسم الزوج بالكامل (رباعي)',
            'جوال الزوج',
            'رقم هوية الزوجة',
            'اسم الزوجة بالكامل (رباعي)'
        ];
    }

    public function array(): array
    {
        return [
            ['405242876', 'محمد أحمد محمود علي', '0599123456', '801761826', 'فاطمة حسن يوسف خليل']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
