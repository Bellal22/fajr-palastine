<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class KitchenCardsTemplateExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'اسم المخيم',
            'رقم البطاقة'
        ];
    }

    public function array(): array
    {
        return [
            ['مخيم النور', '123456789'],
            ['مخيم الأمل', '987654321']
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
