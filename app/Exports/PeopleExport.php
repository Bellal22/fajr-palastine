<?php

namespace App\Exports;

use App\Models\Person;
use DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;


class PeopleExport implements FromQuery, WithHeadings,WithChunkReading
{
    use Exportable;
    private $persons;
    private $filtered;

    public function __construct($persons = [], $filtered = false)
    {
        $this->persons = $persons;
        $this->filtered = $filtered;
    }

    public function query()
    {
        if (!empty($this->persons)) {
            $query = Person::query();
            $query->whereIn('id', $this->persons);
            return $query;
        }
        return DB::table('persons')
            ->select([
                'id_num',
                'first_name',
                'father_name',
                'grandfather_name',
                'family_name',
                'gender',
                'phone',
                'dob',
                'social_status',
                'city',
                'current_city',
                'neighborhood',
                'landmark',
                'housing_type',
                'housing_damage_status',
                'employment_status',
                'person_status',
                'relatives_count',
                'has_condition',
                'condition_description',
            ])->orderBy('id');


    }

//    public function map($row): array
//    {
//        static $index = 0;
//        $index++;
//
//        return [
//            $index,
//            $row->id_num,
//            $row->first_name,
//            $row->father_name,
//            $row->grandfather_name,
//            $row->family_name,
//            $row->gender,
//            $row->phone,
//            $row->dob,
//            $row->social_status,
//            $row->city,
//            $row->current_city,
//            $row->neighborhood,
//            $row->landmark,
//            $row->housing_type,
//            $row->housing_damage_status,
//            $row->employment_status,
//            $row->person_status,
//            $row->relatives_count,
//            $row->has_condition,
//            $row->condition_description,
//        ];
//    }

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
        return 1000;
    }

}
