<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MobileWorkerNominationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    protected $projectId;
    protected $userId;

    public function __construct($projectId, $userId)
    {
        $this->projectId = $projectId;
        $this->userId = $userId;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function collection()
    {
        return DB::table('project_beneficiaries')
            ->join('persons', 'persons.id', '=', 'project_beneficiaries.person_id')
            ->where('project_beneficiaries.project_id', $this->projectId)
            ->where('project_beneficiaries.user_id', $this->userId)
            ->select('persons.first_name', 'persons.father_name', 'persons.family_name', 'persons.id_num', 'persons.phone', 'project_beneficiaries.created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'الاسم الرباعي',
            'رقم الهوية',
            'رقم الجوال',
            'تاريخ الترشيح'
        ];
    }

    public function map($row): array
    {
        return [
            $row->first_name . ' ' . $row->father_name . ' ' . $row->family_name,
            $row->id_num,
            $row->phone,
            \Carbon\Carbon::parse($row->created_at)->format('Y-m-d')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            5 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F81BD']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $project = \App\Models\Project::find($this->projectId);
                $user = \App\Models\User::find($this->userId);
                
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'كشف ترشيحات موظف الميدان: ' . $user->name);
                $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'المشروع: ' . $project->name);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A3:D3');
                $day = \Carbon\Carbon::now()->locale('ar')->dayName;
                $date = \Carbon\Carbon::now()->format('Y-m-d');
                
                $packages = $project->readyPackages->pluck('name')->merge($project->internalPackages->pluck('name'))->implode(' - ');
                $pkgText = $packages ? " | الطرود: {$packages}" : "";

                $sheet->setCellValue('A3', "تاريخ التصدير: {$day} {$date}{$pkgText}");
                $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');

                $sheet->setRightToLeft(true);
            },
        ];
    }
}
