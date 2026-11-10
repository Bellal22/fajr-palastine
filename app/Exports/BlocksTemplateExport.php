<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BlocksTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'اسم المندوب',
            'المسمى الوظيفي (اسم المخيم)',
            'رقم الهاتف',
            'ملاحظات',
            'خط العرض',
            'خط الطول',
        ];
    }

    public function array(): array
    {
        return [
            [
                'فارس عطا مصلح وافي',
                'مندوب مخيم العطاء1',
                '0599484971',
                'ملاحظة تجريبية',
                '31.3461',
                '34.3064',
            ],
        ];
    }
}
