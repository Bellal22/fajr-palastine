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

        // 1. جلب البيانات موزعة حسب الدفعات (لأخذ المحرز والملاحظات بشكل دقيق)
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
                'pb.user_id',
                'pb.created_at',
                DB::raw('SUM(pb.quantity) as distributed_count'),
                DB::raw('MAX(pb.captured_qty) as captured_qty'),
                DB::raw('MAX(pb.external_notes) as external_notes')
            )
            ->where('pb.status', 'مستلم')
            ->whereBetween('pb.delivery_date', [$dateFrom, $dateTo]);

        if ($this->warehouse) {
            $distributionsQuery->where('pb.sub_warehouse_id', $this->warehouse->id);
        }

        $rawDistributions = $distributionsQuery
            ->groupBy('pb.delivery_date', 'pb.sub_warehouse_id', 'sw.name', 'pb.project_id', 'prj.name', 'prj.description', 'pb.user_id', 'pb.created_at')
            ->orderBy('pb.delivery_date')
            ->get();

        // 2. تجميع الدفعات في سطر واحد لكل (تاريخ، نقطة، مشروع)
        $aggregated = [];
        foreach ($rawDistributions as $row) {
            $date = Carbon::parse($row->delivery_date)->toDateString();
            $key = $date . '_' . $row->sub_warehouse_id . '_' . $row->project_id;
            
            if (!isset($aggregated[$key])) {
                $aggregated[$key] = (object) [
                    'delivery_date' => $date,
                    'sub_warehouse_id' => $row->sub_warehouse_id,
                    'warehouse_name' => $row->warehouse_name,
                    'project_id' => $row->project_id,
                    'project_name' => $row->project_name,
                    'project_description' => $row->project_description,
                    'distributed_count' => 0,
                    'captured_qty' => 0,
                    'notes_array' => [],
                ];
            }
            
            $aggregated[$key]->distributed_count += $row->distributed_count;
            $aggregated[$key]->captured_qty += ($row->captured_qty ?? 0);
            
            if ($row->external_notes && $row->external_notes != '-' && !in_array($row->external_notes, $aggregated[$key]->notes_array)) {
                $aggregated[$key]->notes_array[] = $row->external_notes;
            }
        }

        $data = [];
        $i = 1;

        foreach ($aggregated as $row) {
            // 3. جلب أنواع الطرود
            $packageTypes = DB::table('coupon_types as ct')
                ->join('project_coupon_types as pct', 'ct.id', '=', 'pct.coupon_type_id')
                ->where('pct.project_id', $row->project_id)
                ->pluck('ct.name')
                ->implode(', ');

            // 4. جلب الوارد (تراكمي حتى ذلك التاريخ)
            $inboundCount = DB::table('outbound_shipment_items as osi')
                ->join('outbound_shipments as os', 'osi.outbound_shipment_id', '=', 'os.id')
                ->where('os.sub_warehouse_id', $row->sub_warehouse_id)
                ->where('os.project_id', $row->project_id)
                ->whereDate('os.created_at', '<=', $row->delivery_date)
                ->sum('osi.quantity');

            // 5. حساب المتبقي (الوارد - إجمالي المنصرف التراكمي)
            $totalDistributedSoFar = DB::table('project_beneficiaries')
                ->where('sub_warehouse_id', $row->sub_warehouse_id)
                ->where('project_id', $row->project_id)
                ->where('status', 'مستلم')
                ->whereDate('delivery_date', '<=', $row->delivery_date)
                ->sum('quantity');

            $remaining = $inboundCount - $totalDistributedSoFar;
            $recordedMahrouz = $row->captured_qty;
            $diff = $remaining - $recordedMahrouz;

            $data[] = [
                'serial' => $i++,
                'date' => Carbon::parse($row->delivery_date)->format('Y/m/d'),
                'warehouse' => $row->warehouse_name,
                'package_type' => $packageTypes ?: '-', 
                'package_content' => $row->project_description ?? '-',
                'inbound' => $inboundCount ?: 0,
                'distributed' => $row->distributed_count,
                'remaining' => $remaining < 0 ? 0 : $remaining,
                'recorded_mahrouz' => $recordedMahrouz,
                'match' => $diff == 0 ? 'مطابق' : 'فرق: ' . $diff,
                'notes' => count($row->notes_array) > 0 ? implode(' | ', $row->notes_array) : '-',
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
            'محرز (تلقائي)',
            'محرز (مسجل)',
            'المطابقة',
            'ملاحظات الميدان',
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // يتم استدعاؤه بعد الانتهاء من تعبئة الورقة
            },
        ];
    }
}
