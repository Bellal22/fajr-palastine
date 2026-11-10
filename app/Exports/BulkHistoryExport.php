<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;

class BulkHistoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $idNumbers;
    protected $mergeRanges = [];

    public function __construct(array $idNumbers)
    {
        $this->idNumbers = $idNumbers;
    }

    public function collection()
    {
        $data = collect();
        $currentRow = 2; // البيانات تبدأ من الصف الثاني

        foreach ($this->idNumbers as $id) {
            $id = trim($id);
            if (empty($id)) continue;

            $person = Person::where('id_num', $id)->first();
            $isIncomplete = false;
            $isEmployee = false;
            
            if (!$person) {
                $person = DB::table('incomplete_people')->where('id_num', $id)->first();
                if (!$person) {
                    $person = DB::table('employees')->where('id_num', $id)->first();
                    if (!$person) continue;
                    $isEmployee = true;
                } else {
                    $isIncomplete = true;
                }
            } else {
                // العودة لرب الأسرة لجلب السجل الموحد
                $person = $person->findUltimateHead();
            }

            if ($isEmployee) {
                $idField = 'employee_id';
            } elseif ($isIncomplete) {
                $idField = 'incomplete_person_id';
            } else {
                $idField = 'person_id';
            }
            
            $coupons = DB::table('project_beneficiaries')
                ->join('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
                ->leftJoin('sub_warehouses', 'sub_warehouses.id', '=', 'project_beneficiaries.sub_warehouse_id')
                ->select(
                    'projects.name as project_name',
                    'project_beneficiaries.status',
                    'project_beneficiaries.quantity',
                    'project_beneficiaries.delivery_date',
                    'project_beneficiaries.created_at as nomination_date',
                    'sub_warehouses.name as warehouse_name',
                    'project_beneficiaries.notes'
                )
                ->where('project_beneficiaries.' . $idField, $person->id)
                ->orderBy('project_beneficiaries.delivery_date', 'desc')
                ->get();

            $rowCount = $coupons->count() ?: 1;
            if ($rowCount > 1) {
                // تخزين نطاقات الدمج (الأعمدة A, B, C)
                $endRow = $currentRow + $rowCount - 1;
                $this->mergeRanges[] = "A{$currentRow}:A{$endRow}";
                $this->mergeRanges[] = "B{$currentRow}:B{$endRow}";
                $this->mergeRanges[] = "C{$currentRow}:C{$endRow}";
            }

            $baseInfo = [
                'full_name' => $isEmployee ? ($person->first_name . ' ' . ($person->family_name ?? '')) : ($isIncomplete ? ($person->first_name . ' ' . ($person->family_name ?? '')) : $person->getFullQuadName()),
                'id_num' => $person->id_num,
                'phone' => $person->phone ?? '-',
            ];

            if ($coupons->isEmpty()) {
                $data->push(array_merge($baseInfo, [
                    'project' => 'لا يوجد سجل كوبونات',
                    'qty' => '-',
                    'status' => '-',
                    'nomination_date' => '-',
                    'delivery_date' => '-',
                    'warehouse' => '-',
                    'notes' => '-'
                ]));
                $currentRow++;
            } else {
                foreach ($coupons as $coupon) {
                    $data->push(array_merge($baseInfo, [
                        'project' => $coupon->project_name,
                        'qty' => $coupon->quantity,
                        'status' => $coupon->status,
                        'nomination_date' => $coupon->nomination_date ? \Carbon\Carbon::parse($coupon->nomination_date)->format('Y-m-d') : '-',
                        'delivery_date' => $coupon->delivery_date ?? '-',
                        'warehouse' => $coupon->warehouse_name ?? '-',
                        'notes' => $coupon->notes ?? '-'
                    ]));
                    $currentRow++;
                }
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'اسم رب الأسرة',
            'رقم الهوية',
            'رقم الجوال',
            'المشروع / الكوبون',
            'الكمية',
            'الحالة',
            'تاريخ الترشيح',
            'تاريخ الاستلام',
            'المخزن / النقطة',
            'ملاحظات الاستلام'
        ];
    }

    public function map($row): array
    {
        return [
            $row['full_name'],
            $row['id_num'],
            $row['phone'],
            $row['project'],
            $row['qty'],
            $row['status'],
            $row['nomination_date'],
            $row['delivery_date'],
            $row['warehouse'],
            $row['notes'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFA500'] // البرتقالي الأساسي
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // تطبيق عمليات الدمج
                foreach ($this->mergeRanges as $range) {
                    $sheet->mergeCells($range);
                    // توسيط النص المدمج عمودياً
                    $sheet->getStyle($range)->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // إضافة حدود لكافة الخلايا
                $highestRow = $event->sheet->getHighestRow();
                $highestColumn = $event->sheet->getHighestColumn();
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
