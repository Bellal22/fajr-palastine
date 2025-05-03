<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Http\Filters\PersonFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PeopleExport implements FromQuery, WithHeadings, WithChunkReading
{
    use Exportable;

    protected array $filters;
    protected $request;

    public function __construct(Request $request, array $filters = [])
    {
        $this->request = $request;
        $this->filters = $filters;
    }

    public function query(): Builder
    {
        $query = Person::query()->select([
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
            'employment_status',
            'has_condition',
        ])->whereNull('relationship')
            ->latest();

        \Log::info('Export Filters Received:', $this->filters);
        // إنشاء مثيل PersonFilter يدويًا وتمرير $query إليه
        $filter = new PersonFilter($query);

        // تطبيق الفلاتر. بما أن BaseFilters يسترد القيم من $this->request،
        // لا تحتاج إلى تمرير أي وسيط هنا.
        return $filter->apply($this->filters);
    }

    public function headings(): array
    {
        return [
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
            'Employment Status',
            'Has Condition',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}