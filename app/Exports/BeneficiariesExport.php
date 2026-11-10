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
        $query = \DB::table('project_beneficiaries')
            ->where('project_id', $this->project->id)
            ->leftJoin('persons', 'persons.id', '=', 'project_beneficiaries.person_id')
            ->leftJoin('incomplete_people', 'incomplete_people.id', '=', 'project_beneficiaries.incomplete_person_id')
            ->leftJoin('employees', 'employees.id', '=', 'project_beneficiaries.employee_id')
            ->leftJoin('sub_warehouses', 'project_beneficiaries.sub_warehouse_id', '=', 'sub_warehouses.id')
            ->select(
                'project_beneficiaries.*',
                \DB::raw('COALESCE(persons.id_num, incomplete_people.id_num, employees.id_num) as id_num'),
                \DB::raw('COALESCE(persons.first_name, incomplete_people.first_name, employees.first_name) as first_name'),
                \DB::raw('COALESCE(persons.father_name, incomplete_people.father_name, employees.father_name) as father_name'),
                \DB::raw('COALESCE(persons.grandfather_name, incomplete_people.grandfather_name, employees.grandfather_name) as grandfather_name'),
                \DB::raw('COALESCE(persons.family_name, incomplete_people.family_name, employees.family_name) as family_name'),
                \DB::raw('COALESCE(persons.phone, incomplete_people.phone, employees.phone) as phone'),
                'sub_warehouses.name as warehouse_name'
            );

        if (!empty($this->request['search'])) {
            $searchValue = $this->request['search'];
            $ids = preg_split('/[\s,]+/', $searchValue, -1, PREG_SPLIT_NO_EMPTY);
            
            $query->where(function($q) use ($ids, $searchValue) {
                $q->whereIn('persons.id_num', $ids)
                  ->orWhereIn('incomplete_people.id_num', $ids)
                  ->orWhereIn('employees.id_num', $ids)
                  ->orWhere('sub_warehouses.name', 'LIKE', "%{$searchValue}%");
            });
        }

        if (!empty($this->request['sub_warehouse_id'])) {
            $query->where('project_beneficiaries.sub_warehouse_id', $this->request['sub_warehouse_id']);
        }

        if (!empty($this->request['status'])) {
            $query->where('project_beneficiaries.status', $this->request['status']);
        }

        if (!empty($this->request['date_from'])) {
            $query->whereDate(\DB::raw('COALESCE(project_beneficiaries.delivery_date, project_beneficiaries.created_at)'), '>=', $this->request['date_from']);
        }

        if (!empty($this->request['date_to'])) {
            $query->whereDate(\DB::raw('COALESCE(project_beneficiaries.delivery_date, project_beneficiaries.created_at)'), '<=', $this->request['date_to']);
        }

        if (!empty($this->request['exact_date'])) {
            $query->whereDate(\DB::raw('COALESCE(project_beneficiaries.delivery_date, project_beneficiaries.created_at)'), '=', $this->request['exact_date']);
        }

        if (!empty($this->request['quantity_from'])) {
            $query->where('project_beneficiaries.quantity', '>=', $this->request['quantity_from']);
        }

        if (!empty($this->request['quantity_to'])) {
            $query->where('project_beneficiaries.quantity', '<=', $this->request['quantity_to']);
        }

        if (!empty($this->request['exact_quantity'])) {
            $query->where('project_beneficiaries.quantity', $this->request['exact_quantity']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'رقم الهوية',
            'الاسم رباعي',
            'رقم الجوال',
            'المخزن الفرعي (نقطة التوزيع)',
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
            $beneficiary->quantity,
            $beneficiary->status,
            $beneficiary->delivery_date,
            $beneficiary->notes
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
