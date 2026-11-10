<?php

namespace App\Exports;

use App\Models\AreaResponsible;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AreaResponsibleExport implements WithMultipleSheets
{
    protected $areaResponsible;
    protected $unserved;

    public function __construct(AreaResponsible $areaResponsible, bool $unserved = false)
    {
        $this->areaResponsible = $areaResponsible;
        $this->unserved = $unserved;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Add Statistics Sheet (First Sheet)
        $sheets[] = new AreaResponsibleStatsSheet($this->areaResponsible, $this->unserved);

        // 2. Add a Sheet for each Block (Delegate) associated with this Area Responsible
        foreach ($this->areaResponsible->blocks as $block) {
            // Optional: Only add blocks that assume they have people?
            // User requested: "عند كل مندوب بشكل تلقائي"
            $sheets[] = new BlockPersonsSheet($block, $this->unserved);
        }

        return $sheets;
    }
}
