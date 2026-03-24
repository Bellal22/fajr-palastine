<?php

namespace App\Exports\Sheets;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProjectBeneficiariesQuerySheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, WithChunkReading, ShouldAutoSize, WithEvents, WithCustomStartCell, ShouldQueue
{
    protected Project $project;
    protected array $filters;
    protected bool $includeStats;

    public function __construct(Project $project, array $filters, bool $includeStats = false)
    {
        $this->project = $project;
        $this->filters = $filters;
        $this->includeStats = $includeStats;
    }

    public function query()
    {
        $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();

        $query = DB::table('persons as p')
            ->join('project_beneficiaries as pb', 'p.id', '=', 'pb.person_id')
            ->leftJoin('area_responsibles as ar', 'p.area_responsible_id', '=', 'ar.id')
            ->leftJoin('blocks as b', 'p.block_id', '=', 'b.id')
            ->select([
                'p.id_num',
                'p.first_name',
                'p.father_name',
                'p.grandfather_name',
                'p.family_name',
                'p.gender',
                'p.phone',
                'p.social_status',
                'p.dob',
                'p.relatives_count',
                'p.city',
                'p.current_city',
                'p.neighborhood',
                'p.employment_status',
                'p.has_condition',
                'p.api_sync_status',
                'ar.name as area_responsible_name',
                'b.name as block_name',
                'pb.quantity',
                'pb.delivery_date',
                'pb.sub_warehouse_id',
                
                // الزوجة 1
                DB::raw('(SELECT id_num FROM persons WHERE relative_id = p.id_num AND relationship = "زوجة" ORDER BY id_num LIMIT 1) AS wife1_id_num'),
                DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name)) FROM persons WHERE relative_id = p.id_num AND relationship = "زوجة" ORDER BY id_num LIMIT 1) AS wife1_full_name'),
                
                // الزوجة 2
                DB::raw('(SELECT id_num FROM persons WHERE relative_id = p.id_num AND relationship = "زوجة" ORDER BY id_num LIMIT 1 OFFSET 1) AS wife2_id_num'),
                DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name)) FROM persons WHERE relative_id = p.id_num AND relationship = "زوجة" ORDER BY id_num LIMIT 1 OFFSET 1) AS wife2_full_name'),
                
                // الزوجة 3
                DB::raw('(SELECT id_num FROM persons WHERE relative_id = p.id_num AND relationship = "زوجة" ORDER BY id_num LIMIT 1 OFFSET 2) AS wife3_id_num'),
                DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name)) FROM persons WHERE relative_id = p.id_num AND relationship = "زوجة" ORDER BY id_num LIMIT 1 OFFSET 2) AS wife3_full_name'),

                // الزوجة 4
                DB::raw('(SELECT id_num FROM persons WHERE relative_id = p.id_num AND relationship = "زوجة" ORDER BY id_num LIMIT 1 OFFSET 3) AS wife4_id_num'),
                DB::raw('(SELECT TRIM(CONCAT(first_name," ",father_name," ",grandfather_name," ",family_name)) FROM persons WHERE relative_id = p.id_num AND relationship = "زوجة" ORDER BY id_num LIMIT 1 OFFSET 3) AS wife4_full_name'),

            ])
            ->where('pb.project_id', $this->project->id)
            ->where('pb.status', 'مستلم')
            ->whereBetween('pb.delivery_date', [$dateFrom, $dateTo]);

        return $query->orderBy('pb.delivery_date', 'asc');
    }

    public function startCell(): string
    {
        return $this->includeStats ? 'A5' : 'A1';
    }

    public function headings(): array
    {
        return [
            '#',
            'رقم الهوية',
            'الاسم الرباعي',
            'الاسم الأول',
            'اسم الأب',
            'اسم الجد',
            'اسم العائلة',
            'الجنس',
            'رقم الهاتف',
            'مسؤول المنطقة',
            'المندوب',
            'الحالة الاجتماعية',
            'تاريخ الميلاد',
            'عدد أفراد الأسرة',
            'هوية الزوجة 1',
            'اسم الزوجة 1 الكامل',
            'هوية الزوجة 2',
            'اسم الزوجة 2 الكامل',
            'هوية الزوجة 3',
            'اسم الزوجة 3 الكامل',
            'هوية الزوجة 4',
            'اسم الزوجة 4 الكامل',
            'المدينة الأصلية',
            'المدينة الحالية',
            'الحي السكني',
            'حالة العمل',
            'لديه حالة صحية',
            'حالة المزامنة',
            'الكمية',
            'تاريخ التسليم',
        ];
    }

    public function map($p): array
    {
        static $i = 0;
        $i++;

        $fullName = trim($p->first_name . ' ' . $p->father_name . ' ' . $p->grandfather_name . ' ' . $p->family_name);

        return [
            $i,
            $p->id_num,
            $fullName,
            $p->first_name,
            $p->father_name,
            $p->grandfather_name,
            $p->family_name,
            $p->gender,
            $p->phone,
            $p->area_responsible_name ?? 'غير متوفر',
            $p->block_name ?? 'غير متوفر',
            __($p->social_status),
            $p->dob,
            $p->relatives_count,
            $p->wife1_id_num,
            $p->wife1_full_name,
            $p->wife2_id_num,
            $p->wife2_full_name,
            $p->wife3_id_num,
            $p->wife3_full_name,
            $p->wife4_id_num,
            $p->wife4_full_name,
            __($p->city),
            __($p->current_city),
            __($p->neighborhood),
            $p->employment_status,
            $p->has_condition ? 'نعم' : 'لا',
            $p->api_sync_status,
            $p->quantity,
            $p->delivery_date,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function title(): string
    {
        $name = mb_substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $this->project->name), 0, 30);
        return $name ?: 'المستلمين';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);
                
                $headerRow = $this->includeStats ? 5 : 1;
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle('A' . $headerRow . ':' . $highestColumn . $headerRow)->applyFromArray([
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

                if ($highestRow > $headerRow) {
                    $sheet->getStyle('A' . ($headerRow + 1) . ':' . $highestColumn . $highestRow)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ]
                    ]);
                }

                if ($this->includeStats) {
                    $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
                    $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();

                    $recipientsCount = DB::table('project_beneficiaries')
                        ->where('project_id', $this->project->id)
                        ->where('status', 'مستلم')
                        ->whereBetween('delivery_date', [$dateFrom, $dateTo])
                        ->count();

                    $sheet->setCellValue('A1', 'المشروع / الكوبون:');
                    $sheet->setCellValue('B1', $this->project->name);
                    $sheet->mergeCells('B1:D1');
                    
                    $sheet->setCellValue('A2', 'فترة التقرير:');
                    $sheet->setCellValue('B2', 'من: ' . $dateFrom->format('Y-m-d') . ' إلى: ' . $dateTo->format('Y-m-d'));
                    $sheet->mergeCells('B2:D2');

                    $sheet->setCellValue('A3', 'عدد المستلمين في الفترة:');
                    $sheet->setCellValue('B3', $recipientsCount);
                    $sheet->mergeCells('B3:D3');

                    $sheet->getStyle('A1:A3')->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'E9ECEF']
                        ],
                    ]);
                    
                    $sheet->getStyle('A1:D3')->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                        ]
                    ]);
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }
}
