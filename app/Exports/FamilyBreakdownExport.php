<?php

namespace App\Exports;

use App\Models\Person;
use App\Http\Controllers\Dashboard\DashboardController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class FamilyBreakdownExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $type;
    protected $search;
    protected $status;
    private $rowIndex = 0;

    public function __construct($type = 'families', $search = '', $status = 'all')
    {
        $this->type = $type;
        $this->search = $search;
        $this->status = $status;
    }

    public function collection()
    {
        $allFamilies = Person::getFamilyBreakdownStats($this->type);

        if (!empty($this->search)) {
            $normalizedSearch = DashboardController::normalizeArabicText($this->search);
            $search = $this->search;
            $allFamilies = array_filter($allFamilies, function ($item) use ($search, $normalizedSearch) {
                return (mb_strpos($item['display_name'], $search) !== false) 
                    || (mb_strpos($item['norm_name'], $normalizedSearch) !== false);
            });
        }

        if ($this->status === 'approved') {
            $allFamilies = array_filter($allFamilies, function ($item) {
                return $item['approved_count'] > 0;
            });
        } elseif ($this->status === 'unapproved') {
            $allFamilies = array_filter($allFamilies, function ($item) {
                return $item['unapproved_count'] > 0;
            });
        }

        return collect(array_values($allFamilies));
    }

    public function headings(): array
    {
        return [
            '#',
            'اسم العائلة',
            'إجمالي المسجلين',
            'المعتمدين',
            'غير المعتمدين',
            'نسبة الاعتماد (%)',
        ];
    }

    public function map($row): array
    {
        $this->rowIndex++;

        $total = $row['total_count'] ?? 0;
        $approved = $row['approved_count'] ?? 0;
        $unapproved = $row['unapproved_count'] ?? 0;
        $percent = $total > 0 ? round(($approved / $total) * 100, 1) . '%' : '0%';

        return [
            $this->rowIndex,
            $row['display_name'],
            $total,
            $approved,
            $unapproved,
            $percent,
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
