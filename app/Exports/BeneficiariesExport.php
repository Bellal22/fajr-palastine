<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeneficiariesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $project;
    private $request;

    public function __construct(Project $project, $request)
    {
        $this->project = $project;
        $this->request = $request;
    }

    public function collection()
    {
        $query = $this->project->beneficiaries()
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id')
            ->leftJoin('sub_warehouses', 'project_beneficiaries.sub_warehouse_id', '=', 'sub_warehouses.id')
            ->select('persons.*', 'sub_warehouses.name as warehouse_name');

        if (!empty($this->request['search'])) {
            $searchValue = $this->request['search'];
            $ids = preg_split('/[\s,]+/', $searchValue, -1, PREG_SPLIT_NO_EMPTY);
            
            if (count($ids) > 1) {
                $query->whereIn('persons.id_num', $ids);
            } else {
                $query->where('persons.id_num', 'LIKE', "%{$searchValue}%");
            }
        }

        if (!empty($this->request['status'])) {
            $query->wherePivot('status', $this->request['status']);
        }

        if (!empty($this->request['date_from'])) {
            $query->wherePivot('delivery_date', '>=', $this->request['date_from']);
        }

        if (!empty($this->request['date_to'])) {
            $query->wherePivot('delivery_date', '<=', $this->request['date_to']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'رقم الهوية',
            'الاسم رباعي',
            'رقم الجوال',
            'المخزن الفرعي',
            'الكمية',
            'الحالة',
            'تاريخ التسليم',
            'الملاحظات'
        ];
    }

    public function map($beneficiary): array
    {
        return [
            $beneficiary->id_num,
            trim("{$beneficiary->first_name} {$beneficiary->father_name} {$beneficiary->grandfather_name} {$beneficiary->family_name}"),
            $beneficiary->phone,
            $beneficiary->warehouse_name ?? 'غير محدد',
            $beneficiary->pivot->quantity,
            $beneficiary->pivot->status,
            $beneficiary->pivot->delivery_date,
            $beneficiary->pivot->notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);
        return [];
    }
}
