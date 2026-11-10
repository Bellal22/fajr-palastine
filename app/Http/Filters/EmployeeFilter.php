<?php

namespace App\Http\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;

class EmployeeFilter extends BaseFilters
{
    protected $filters = [
        'name',
        'id_num',
        'selected_id',
    ];

    protected function name($value)
    {
        if ($value) {
            return $this->builder->where(function ($q) use ($value) {
                $q->where('first_name', 'like', "%{$value}%")
                  ->orWhere('father_name', 'like', "%{$value}%")
                  ->orWhere('grandfather_name', 'like', "%{$value}%")
                  ->orWhere('family_name', 'like', "%{$value}%");
            });
        }
        return $this->builder;
    }

    protected function idNum($value)
    {
        if ($value) {
            return $this->builder->where('id_num', 'like', "%{$value}%");
        }
        return $this->builder;
    }

    public function selectedId($value)
    {
        if ($value) {
            $this->builder->sortingByIds($value);
        }
        return $this->builder;
    }
}
