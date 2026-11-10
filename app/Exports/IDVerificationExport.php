<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class IDVerificationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $idNumbers;

    public function __construct(array $idNumbers)
    {
        $this->idNumbers = $idNumbers;
    }

    public function collection()
    {
        $results = collect();

        foreach ($this->idNumbers as $id) {
            $id = trim($id);
            if (empty($id)) continue;

            $isValid = $this->isLuhnValid($id);

            $results->push([
                'id_num' => $id,
                'status' => $isValid ? 'صحيح' : 'خطأ (غير مطابق لمعادلة الهوية الفلسطينية)',
            ]);
        }

        return $results;
    }

    protected function isLuhnValid($id)
    {
        // إزالة أي مسافات أو رموز
        $id = preg_replace('/\D/', '', $id);
        
        // الهوية الفلسطينية/الإسرائيلية يجب أن تكون 9 أرقام (يمكن قبول أقل مع إضافة أصفار في البداية)
        if (strlen($id) > 9) return false;
        $id = str_pad($id, 9, '0', STR_PAD_LEFT);

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $digit = (int)$id[$i];
            $step = $digit * (($i % 2) + 1);
            if ($step > 9) $step -= 9;
            $sum += $step;
        }

        return ($sum % 10 === 0);
    }

    public function headings(): array
    {
        return [
            'رقم الهوية',
            'حالة الفحص (معادلة لونا)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500'] // برتقالي
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        return [];
    }
}
