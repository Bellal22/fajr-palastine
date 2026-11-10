<?php

namespace App\Exports\Sheets;

use App\Models\SubWarehouse;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubWarehouseDetailedReportSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithEvents, ShouldQueue
{
    protected array $filters;
    protected ?SubWarehouse $warehouse;
    protected string $sheetTitle;

    public function __construct(array $filters, ?SubWarehouse $warehouse, string $sheetTitle)
    {
        $this->filters = $filters;
        $this->warehouse = $warehouse;
        $this->sheetTitle = $sheetTitle;
    }

    public function collection()
    {
        $dateFrom = Carbon::parse($this->filters['date_from'])->startOfDay();
        $dateTo = Carbon::parse($this->filters['date_to'])->endOfDay();

        $query = DB::table('project_beneficiaries as pb')
            ->join('projects as prj', 'pb.project_id', '=', 'prj.id')
            ->leftJoin('persons as p', 'pb.person_id', '=', 'p.id')
            ->leftJoin('incomplete_people as ip', 'pb.incomplete_person_id', '=', 'ip.id')
            ->leftJoin('area_responsibles as ar', function($join) {
                $join->on('p.area_responsible_id', '=', 'ar.id')
                     ->orOn('ip.area_responsible_id', '=', 'ar.id');
            })
            ->leftJoin('blocks as b', function($join) {
                $join->on('p.block_id', '=', 'b.id')
                     ->orOn('ip.block_id', '=', 'b.id');
            })
            ->select(
                'pb.delivery_date',
                'pb.project_id',
                'prj.name as project_name',
                'prj.description as project_description',
                'p.current_city as p_city',
                'ip.current_city as ip_city',
                'ar.name as area_name',
                'ar.address as area_address',
                'b.name as block_name',
                'b.lat',
                'b.lan',
                'pb.status',
                'pb.external_notes',
                'pb.sub_warehouse_id'
            )
            ->whereBetween('pb.delivery_date', [$dateFrom, $dateTo]);

        if ($this->warehouse) {
            $query->where('pb.sub_warehouse_id', $this->warehouse->id);
        }

        $results = $query->get();

        // تجميع البيانات حسب (التاريخ، المشروع، نقطة التوزيع)
        $grouped = [];
        $projectIds = [];
        foreach ($results as $row) {
            $date = Carbon::parse($row->delivery_date)->toDateString();
            $key = $date . '_' . $row->project_id . '_' . $row->sub_warehouse_id;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'date' => $date,
                    'project_id' => $row->project_id,
                    'project_name' => $row->project_name,
                    'project_description' => $row->project_description,
                    'cities' => [],
                    'areas' => [],
                    'blocks' => [],
                    'addresses' => [],
                    'coordinates' => [],
                    'planned_families' => 0,
                    'actual_families' => 0,
                    'notes' => [],
                ];
                $projectIds[] = $row->project_id;
            }

            $grouped[$key]['planned_families']++;
            if ($row->status == 'مستلم') {
                $grouped[$key]['actual_families']++;
            }

            $city = $row->p_city ?: $row->ip_city;
            if ($city && !in_array($city, $grouped[$key]['cities'])) {
                $grouped[$key]['cities'][] = $city;
            }

            if ($row->area_name && !in_array($row->area_name, $grouped[$key]['areas'])) {
                $grouped[$key]['areas'][] = $row->area_name;
            }

            if ($row->block_name && !in_array($row->block_name, $grouped[$key]['blocks'])) {
                $grouped[$key]['blocks'][] = $row->block_name;
            }

            if ($row->area_address && !in_array($row->area_address, $grouped[$key]['addresses'])) {
                $grouped[$key]['addresses'][] = $row->area_address;
            }

            if ($row->lat && $row->lan) {
                $coord = $row->lat . ',' . $row->lan;
                if (!in_array($coord, $grouped[$key]['coordinates'])) {
                    $grouped[$key]['coordinates'][] = $coord;
                }
            }

            if ($row->external_notes && $row->external_notes != '-' && !in_array($row->external_notes, $grouped[$key]['notes'])) {
                $grouped[$key]['notes'][] = $row->external_notes;
            }
        }

        // جلب البيانات الإضافية للمشاريع دفعة واحدة لتجنب N+1
        $projectIds = array_unique($projectIds);
        $projectExtraData = [];
        if (!empty($projectIds)) {
            $projects = \App\Models\Project::with(['grantingEntities', 'couponTypes'])->whereIn('id', $projectIds)->get();
            foreach ($projects as $project) {
                $projectExtraData[$project->id] = [
                    'organization' => $project->grantingEntities->pluck('name')->implode(', '),
                    'coupons' => $project->couponTypes->pluck('name')->implode(', '),
                ];
            }
        }

        $data = [];
        foreach ($grouped as $row) {
            $extra = $projectExtraData[$row['project_id']] ?? ['organization' => '-', 'coupons' => '-'];
            $items = $extra['coupons'] . ($row['project_description'] ? ' (' . $row['project_description'] . ')' : '');

            $carbonDate = Carbon::parse($row['date']);
            
            $data[] = [
                'organization' => $extra['organization'] ?: '-',
                'week' => $carbonDate->weekOfYear,
                'date' => $carbonDate->format('Y/m/d'),
                'government' => count($row['cities']) > 0 ? implode(', ', $row['cities']) : '-',
                'area' => count($row['areas']) > 0 ? implode(', ', $row['areas']) : '-',
                'site_name' => count($row['blocks']) > 0 ? implode(', ', $row['blocks']) : '-',
                'full_address' => count($row['addresses']) > 0 ? implode(', ', $row['addresses']) : '-',
                'coordinates' => count($row['coordinates']) > 0 ? implode(' | ', $row['coordinates']) : '-',
                'distributed_items' => $items,
                'planned_families' => $row['planned_families'],
                'planned_individuals' => $row['planned_families'] * 5,
                'actual_families' => $row['actual_families'],
                'actual_individuals' => $row['actual_families'] * 5,
                'notes' => count($row['notes']) > 0 ? implode(' | ', $row['notes']) : '-',
            ];
        }
        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            'المؤسسة / المنظمة',
            'الأسبوع',
            'التاريخ',
            'المحافظة',
            'المنطقة / الحي السكني',
            'اسم الموقع / نقطة التوزيع',
            'العنوان الكامل',
            'الإحداثيات (GPS)',
            'المواد الموزعة',
            'عدد العائلات المخطط استهدافها',
            'إجمالي عدد الأفراد المخطط استهدافهم',
            'عدد العائلات المستلمة فعلياً',
            'إجمالي عدد الأفراد المستلمين',
            'ملاحظات الميدان',
        ];
    }

    public function title(): string
    {
        return 'تقرير تفصيلي للمخازن';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);

        if ($highestRow > 1) {
            $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                ]
            ]);
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Formatting if needed
            },
        ];
    }
}
