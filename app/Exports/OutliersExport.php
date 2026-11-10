<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OutliersExport implements WithMultipleSheets
{
    protected $overServed;
    protected $neglected;

    public function __construct($overServed, $neglected)
    {
        $this->overServed = $overServed;
        $this->neglected = $neglected;
    }

    public function sheets(): array
    {
        return [
            new OverServedSheet($this->overServed),
            new NeglectedSheet($this->neglected)
        ];
    }
}
