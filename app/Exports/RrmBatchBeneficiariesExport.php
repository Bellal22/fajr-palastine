<?php

namespace App\Exports;

use App\Models\RrmBatch;
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
use Illuminate\Support\Collection;

class RrmBatchBeneficiariesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected RrmBatch $batch;
    protected Collection $rows;
    protected int $rowCounter = 0;

    public function __construct(RrmBatch $batch, Collection $rows)
    {
        $this->batch = $batch;
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function map($row): array
    {
        $this->rowCounter++;
        $cashInfo = '-';
        if (optional($this->batch->customProject)->is_cash) {
            $cashInfo = $this->batch->customProject->formatted_cash_value ?: '-';
        }

        return [
            $this->rowCounter,
            $row['id_num'] ?? '-',
            $row['name'] ?? '-',
            $row['phone'] ?? '-',
            $row['area_name'] ?? '-',
            $row['warehouse_name'] ?? '-',
            $cashInfo,
            $row['parcels_summary'] ?? '-',
            $row['total_quantity'] ?? 1,
            $row['delivery_date'] ?? '-',
            $row['status'] ?? 'مستلم',
        ];
    }

    public function headings(): array
    {
        return [
            'م',
            'رقم الهوية',
            'الاسم رباعي',
            'رقم الجوال',
            'المنطقة / المندوب',
            'المخزن',
            'المبلغ النقدي',
            'الطرود المستلمة والكميات',
            'إجمالي الكمية',
            'تاريخ الاستلام',
            'الحالة',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5B21B6'], // Deep Purple for RRM
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A1:K' . ($this->rows->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return mb_substr($this->batch->name ?: 'كشف RRM', 0, 30);
    }
}
