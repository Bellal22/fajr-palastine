<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;


class PeopleExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
{
    use Exportable;
    private $persons;
//    private $filtered;

    public function __construct($persons = [])
    {
        $this->persons = $persons;
//        $this->filtered = $filtered;
    }

    public function query()
    {
        $query = Person::query();

        if (!empty($this->persons)) {
            $query->whereIn('id', $this->persons);
        }

        return $query;
    }

    public function map($person): array
    {
        return [
            '', // Placeholder for index, Excel adds it if needed
            $person->id_num,
            $person->first_name,
            $person->father_name,
            $person->grandfather_name,
            $person->family_name,
            $person->gender,
            $person->phone,
            $person->dob,
            $person->social_status,
            $person->city,
            $person->current_city,
            $person->neighborhood,
            $person->landmark,
            $person->housing_type,
            $person->housing_damage_status,
            $person->employment_status,
            $person->person_status,
            $person->relatives_count,
            $person->has_condition,
            $person->condition_description,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'ID Number',
            'First Name',
            'Father Name',
            'Grandfather Name',
            'Family Name',
            'Gender',
            'Phone',
            'Date of Birth',
            'Social Status',
            'City',
            'Current City',
            'Neighborhood',
            'Landmark',
            'Housing Type',
            'Housing Damage Status',
            'Employment Status',
            'Person Status',
            'Relatives Count',
            'Has Condition',
            'Condition Description',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

}
