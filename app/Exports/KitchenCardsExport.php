<?php

namespace App\Exports;

use App\Models\KitchenCard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class KitchenCardsExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    public function collection()
    {
        return KitchenCard::select('camp_name', 'card_number', 'barcode', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'اسم المخيم',
            'رقم البطاقة',
            'الباركود',
            'تاريخ الإضافة'
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
