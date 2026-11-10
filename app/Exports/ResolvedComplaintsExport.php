<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ResolvedComplaintsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function map($complaint): array
    {
        static $i = 0;
        $i++;

        $person = $complaint->person;
        $personName = $person ? trim($person->first_name . ' ' . $person->father_name . ' ' . $person->family_name) : '-';

        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل / مقبول',
            'rejected' => 'مرفوض',
        ];

        return [
            $i,
            $complaint->id_num ?? '-',
            $personName,
            $complaint->complaintType->name ?? 'عام',
            $complaint->admin_complaint_title ?: ($complaint->complaint_title ?? '-'),
            $complaint->admin_complaint_text ?: ($complaint->complaint_text ?? '-'),
            $complaint->created_at ? $complaint->created_at->format('Y-m-d H:i') : '-',
            $complaint->response_title ?? '-',
            $complaint->response ?? '-',
            $complaint->responded_at ? $complaint->responded_at->format('Y-m-d H:i') : ($complaint->updated_at ? $complaint->updated_at->format('Y-m-d H:i') : '-'),
            $complaint->responder->name ?? 'المشرف',
            $statusLabels[$complaint->status] ?? ($complaint->status ?: 'تم الرد'),
        ];
    }

    public function headings(): array
    {
        return [
            'م',
            'رقم الهوية',
            'اسم صاحب الشكوى',
            'نوع الشكوى',
            'عنوان الشكوى المعتمد (الموظف)',
            'تفاصيل الشكوى المعتمدة (الموظف)',
            'تاريخ تقديم الشكوى',
            'عنوان الحل / إجراء الجمعية',
            'نص الرد / تفاصيل الحل',
            'تاريخ الحل',
            'الموظف الذي قام بالحل',
            'حالة الشكوى',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        return [];
    }
}
