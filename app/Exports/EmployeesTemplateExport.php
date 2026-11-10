<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class EmployeesTemplateExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'رقم الهوية',
            'الاسم بالكامل (رباعي)',
            'رقم الجوال',
            'المسمى الوظيفي',
            'الجنس (ذكر/أنثى)',
            'تاريخ الميلاد (YYYY-MM-DD)',
            'ملاحظات'
        ];
    }

    public function array(): array
    {
        return [
            ['400112233', 'أحمد محمد محمود علي', '0599123456', 'موظف إداري', 'ذكر', '1990-05-15', 'موظف مثبت']
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
