<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeopleExport implements FromView, WithStyles
{
    use Exportable;

    private array $persons;

    public function __construct($persons)
    {
        $this->persons = $persons;
    }

    public function view(): View
    {
        return view('dashboard.people.partials.export', [
            'persons' => Person::whereIn('id', $this->persons)->get()
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // تفعيل الاتجاه من اليمين إلى اليسار
        $sheet->setRightToLeft(true);

        // تحديد الأعمدة لتكون عرضها تلقائي بناءً على المحتوى
        foreach (range('A', 'U') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // تغيير ارتفاع الصف الأول
        $sheet->getRowDimension(1)->setRowHeight(30); // زيادة الارتفاع للوضوح

        // تنسيق الخط للصف الأول
        $sheet->getStyle('A1:U1')->getFont()->setBold(true)->setSize(12);

        // توسيط النص في الصف الأول (العناوين)
        $sheet->getStyle('A1:U1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // تنسيق عمود الرقم (A)
        $sheet->getStyle('A')->getFont()->setSize(11);
        $sheet->getStyle('A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // تنسيق باقي الأعمدة لتكون لليمين
        $sheet->getStyle('B:U')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // إضافة الحدود لجميع الخلايا
        $sheet->getStyle('A1:U' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // تلوين الصف الأول
        $sheet->getStyle('A1:U1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFA500'); // اللون البرتقالي للصف الأول

        // تلوين العمود الأول بالكامل
        $column = 'A';
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("{$column}1:{$column}{$highestRow}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFA500'); // تلوين العمود الأول باللون البرتقالي

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
