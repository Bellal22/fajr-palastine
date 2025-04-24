<?php

namespace App\Http\Filters;

use DB;

class PersonFilter extends BaseFilters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [
        'id_num',
        'selected_id',
        'gender',
        'relatives_count',
        'dob',
        'social_status',
        'current_city',
        'neighborhood',
        'dob_from',
        'dob_to',
        'family_members_min',
        'family_members_max',
        'has_condition',
    ];

    /**
     * Filter the query by a given name.
     *
     * @param string|int|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function idNum($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('id_num', $value); // تطابق تام فقط
        }

        return $this->builder;
    }

    /**
     * Sorting results by the given id.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function selectedId($value)
    {
        if ($value) {
            $this->builder->sortingByIds($value);
        }

        return $this->builder;
    }
    /**
     * Filter by gender.
     *
     * @param string|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function gender($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('gender', $value);
        }

        return $this->builder;
    }

    /**
     * Filter by relatives count.
     *
     * @param int|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function relativesCount($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('relatives_count', $value);
        }

        return $this->builder;
    }

    /**
     * Filter by date of birth.
     *
     * @param string|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function dob($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('dob','like' ,$value.'%');
        }

        return $this->builder;
    }
    protected function dobFrom($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->whereDate('dob', '>=', $value);
        }

        return $this->builder;
    }
    protected function dobTo($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->whereDate('dob', '<=', $value);
        }

        return $this->builder;
    }

    /**
     * Filter by social status.
     *
     * @param string|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function socialStatus($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('social_status', $value);
        }

        return $this->builder;
    }

    /**
     * Filter by current city.
     *
     * @param string|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function currentCity($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('current_city', $value);
        }

        return $this->builder;
    }

    protected function neighborhood($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('neighborhood', $value);
        }

        return $this->builder;
    }

    protected function familyMembersMin($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('relatives_count', '>=' ,$value);

        }

        return $this->builder;
    }

    protected function familyMembersMax($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('relatives_count', '<=' ,$value);
        }

        return $this->builder;
    }
    protected function hasCondition($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('has_condition',$value);
        }

        return $this->builder;
    }

}