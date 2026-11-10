<?php

namespace App\Exports;

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 0);
ini_set('max_input_time', -1);

use App\Models\SubWarehouse;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubWarehouseDistributionExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable;

    protected array $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Stats Sheet
        $sheets[] = new \App\Exports\Sheets\SubWarehouseStatsSheet($this->filters);

        // 2. Consolidated Data Sheet
        $subWarehouseId = $this->filters['sub_warehouse_id'] ?? null;
        $warehouse = $subWarehouseId ? \App\Models\SubWarehouse::find($subWarehouseId) : null;
        
        $consolidatedTitle = $warehouse ? "تقرير مجمع - " . $warehouse->name : "تقرير مجمع لكافة النقاط";
        $sheets[] = new \App\Exports\Sheets\SubWarehouseDataSheet($this->filters, $warehouse, $consolidatedTitle);

        // 3. Detailed Report Sheet
        $sheets[] = new \App\Exports\Sheets\SubWarehouseDetailedReportSheet($this->filters, $warehouse, "تقرير تفصيلي");

        // 3. Monthly Data Sheets
        $dateFrom = Carbon::parse($this->filters['date_from'])->startOfMonth();
        $dateTo = Carbon::parse($this->filters['date_to'])->endOfMonth();
        
        $currentMonth = $dateFrom->copy();
        while ($currentMonth <= $dateTo) {
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();
            
            // Adjust to filter range
            if ($monthStart < Carbon::parse($this->filters['date_from'])) {
                $monthStart = Carbon::parse($this->filters['date_from'])->startOfDay();
            }
            if ($monthEnd > Carbon::parse($this->filters['date_to'])) {
                $monthEnd = Carbon::parse($this->filters['date_to'])->endOfDay();
            }
            
            $monthFilters = $this->filters;
            $monthFilters['date_from'] = $monthStart->format('Y-m-d');
            $monthFilters['date_to'] = $monthEnd->format('Y-m-d');
            
            // Generate month title in Arabic
            $monthTitle = $currentMonth->translatedFormat('F Y');
            
            $sheets[] = new \App\Exports\Sheets\SubWarehouseDataSheet($monthFilters, $warehouse, $monthTitle);
            
            $currentMonth->addMonth();
        }

        return $sheets;
    }
}
