<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;

class ProjectBeneficiariesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function query()
    {
        return $this->project->beneficiaries()
            ->wherePivot('status', 'مستلم')
            ->select('persons.*', 'project_beneficiaries.status', 'project_beneficiaries.delivery_date', 'project_beneficiaries.notes as pivot_notes')
            ->with(['areaResponsible', 'block']); 
    }

    public function map($beneficiary): array
    {
        return [
            $beneficiary->id_num,
            $beneficiary->getFullName(), // Assuming this method exists or concat attributes
            $beneficiary->phone,
            $beneficiary->areaResponsible->name ?? '-',
            $beneficiary->block->name ?? '-', // Delegate Name
            $beneficiary->pivot->status,
            $beneficiary->pivot->delivery_date ?? '-',
            $beneficiary->pivot->notes ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'رقم الهوية',
            'الاسم رباعي',
            'رقم الهاتف',
            'المنطقة',
            'المندوب (المربع)',
            'حالة الاستلام',
            'تاريخ الاستلام',
            'ملاحظات',
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

    public function title(): string
    {
        return 'كشف المستفيدين التفصيلي';
    }
}
