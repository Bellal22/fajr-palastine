<?php

namespace App\Exports;

use App\Models\AreaResponsible;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AreaResponsibleExport implements WithMultipleSheets
{
    protected $areaResponsible;

    public function __construct(AreaResponsible $areaResponsible)
    {
        $this->areaResponsible = $areaResponsible;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Add Statistics Sheet (First Sheet)
        $sheets[] = new AreaResponsibleStatsSheet($this->areaResponsible);

        // 2. Add a Sheet for each Block (Delegate) associated with this Area Responsible
        foreach ($this->areaResponsible->blocks as $block) {
            // Optional: Only add blocks that assume they have people?
            // User requested: "عند كل مندوب بشكل تلقائي"
            $sheets[] = new BlockPersonsSheet($block);
        }

        return $sheets;
    }
}
