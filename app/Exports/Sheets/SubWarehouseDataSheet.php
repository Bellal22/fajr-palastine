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

class SubWarehouseDataSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithEvents, ShouldQueue
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

        // 1. Get Distribution Data (عدد المستلمين)
        $distributionsQuery = DB::table('project_beneficiaries as pb')
            ->join('projects as prj', 'pb.project_id', '=', 'prj.id')
            ->join('sub_warehouses as sw', 'pb.sub_warehouse_id', '=', 'sw.id')
            ->select(
                'pb.delivery_date',
                'pb.sub_warehouse_id',
                'sw.name as warehouse_name',
                'pb.project_id',
                'prj.name as project_name',
                'prj.description as project_description',
                DB::raw('SUM(pb.quantity) as distributed_count')
            )
            ->where('pb.status', 'مستلم')
            ->whereBetween('pb.delivery_date', [$dateFrom, $dateTo]);

        if ($this->warehouse) {
            $distributionsQuery->where('pb.sub_warehouse_id', $this->warehouse->id);
        }

        $distributions = $distributionsQuery
            ->groupBy('pb.delivery_date', 'pb.sub_warehouse_id', 'sw.name', 'pb.project_id', 'prj.name', 'prj.description')
            ->orderBy('pb.delivery_date')
            ->get();

        $data = [];
        $i = 1;

        foreach ($distributions as $row) {
            // 2. Get Package Type (CouponType names)
            $packageTypes = DB::table('coupon_types as ct')
                ->join('project_coupon_types as pct', 'ct.id', '=', 'pct.coupon_type_id')
                ->where('pct.project_id', $row->project_id)
                ->pluck('ct.name')
                ->implode(', ');

            // 3. Get Inbound Data (وارد النقاط)
            // Note: We sum up ALL items sent for this project to this warehouse up to this date
            // OR specifically on this date? The user example seems to imply a cumulative or daily "Inbound" value.
            // "الوارد بيتاخد من إرسالية الصادر"
            
            $inboundCount = DB::table('outbound_shipment_items as osi')
                ->join('outbound_shipments as os', 'osi.outbound_shipment_id', '=', 'os.id')
                ->where('os.sub_warehouse_id', $row->sub_warehouse_id)
                ->where('os.project_id', $row->project_id)
                ->whereDate('os.created_at', '<=', $row->delivery_date) // Cumulative
                ->sum('osi.quantity');

            // Calculation of "Mahrouz" (محرز)
            // Remaining = Total Inbound - Total Distributed up to that date
            $totalDistributedSoFar = DB::table('project_beneficiaries')
                ->where('sub_warehouse_id', $row->sub_warehouse_id)
                ->where('project_id', $row->project_id)
                ->where('status', 'مستلم')
                ->whereDate('delivery_date', '<=', $row->delivery_date)
                ->sum('quantity');

            $remaining = $inboundCount - $totalDistributedSoFar;

            $data[] = [
                'serial' => $i++,
                'date' => Carbon::parse($row->delivery_date)->format('Y/m/d'),
                'warehouse' => $row->warehouse_name,
                'package_type' => $packageTypes ?: '-', 
                'package_content' => $row->project_description ?? '-',
                'inbound' => $inboundCount ?: 0,
                'distributed' => $row->distributed_count,
                'remaining' => $remaining < 0 ? 0 : $remaining,
                'notes' => '', // Placeholder for manual notes if needed
            ];
        }

        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            '#',
            'التاريخ',
            'نقطة التوزيع',
            'نوع الطرد',
            'محتوى الطرد',
            'وارد النقاط',
            'عدد المستلمين',
            'محرز',
            'ملاحظات',
        ];
    }

    public function title(): string
    {
        // Excel sheet title must be max 31 chars and no special chars
        $title = mb_substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $this->sheetTitle), 0, 30);
        return $title ?: 'بيانات التوزيع';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
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
}
