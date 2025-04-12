<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;

class PeopleExport implements FromView
{
    use Exportable;
    private $persons;
    private $filtered;

    public function __construct($persons = [],$filtered = 0)
    {
        $this->persons = $persons;
        $this->filtered = $filtered;
    }

    public function view(): View
    {
        return view('dashboard.people.partials.export', [
            'persons' => $this->filtered ? Person::filter()->get() : Person::whereIn('id',$this->persons)->get()
        ]);
    }


}
