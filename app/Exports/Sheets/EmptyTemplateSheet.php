<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmptyTemplateSheet implements FromCollection, WithTitle, WithHeadings
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        return [
            'لا يوجد بيانات',
        ];
    }

    public function title(): string
    {
        return 'لا توجد بيانات';
    }
}
