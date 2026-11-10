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
            ->leftJoin('persons', 'persons.id', '=', 'project_beneficiaries.person_id')
            ->leftJoin('incomplete_people', 'incomplete_people.id', '=', 'project_beneficiaries.incomplete_person_id')
            ->where('project_beneficiaries.project_id', $this->projectId)
            ->where('project_beneficiaries.user_id', $this->userId)
            ->select(
                DB::raw('COALESCE(persons.first_name, incomplete_people.first_name) as first_name'),
                DB::raw('COALESCE(persons.father_name, incomplete_people.father_name) as father_name'),
                DB::raw('COALESCE(persons.grandfather_name, incomplete_people.grandfather_name) as grandfather_name'),
                DB::raw('COALESCE(persons.family_name, incomplete_people.family_name) as family_name'),
                DB::raw('COALESCE(persons.id_num, incomplete_people.id_num) as id_num'),
                DB::raw('COALESCE(persons.phone, incomplete_people.phone) as phone'),
                'project_beneficiaries.status',
                'project_beneficiaries.created_at'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'الاسم الرباعي',
            'رقم الهوية',
            'رقم الجوال',
            'الحالة',
            'تاريخ الترشيح'
        ];
    }

    public function map($row): array
    {
        return [
            trim($row->first_name . ' ' . $row->father_name . ' ' . $row->grandfather_name . ' ' . $row->family_name),
            $row->id_num,
            $row->phone,
            $row->status,
            \Carbon\Carbon::parse($row->created_at)->format('Y-m-d')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            5 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFA500']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $project = \App\Models\Project::find($this->projectId);
                $user = \App\Models\User::find($this->userId);
                
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'كشف ترشيحات موظف الميدان: ' . $user->name);
                $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', 'المشروع: ' . $project->name);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A3:E3');
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
