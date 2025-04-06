<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;

class PeopleExport implements FromView
{
    use Exportable;
    private array $persons;

    public function __construct($persons)
    {
        $this->persons = $persons;
    }

    public function view(): View
    {
        return view('dashboard.people.partials.export', [
            'persons' => Person::whereIn('id',$this->persons)->get()
        ]);
    }


}
