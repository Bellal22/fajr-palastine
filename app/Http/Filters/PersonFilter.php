<?php

namespace App\Http\Filters;

class PersonFilter extends BaseFilters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [
        'name',
        'selected_id',
        'gender',
        'relatives_count',
        'dob',
        'social_status',
        'current_city',
    ];

    /**
     * Filter the query by a given name.
     *
     * @param string|int|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function name($value)
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
    protected function relatives_count($value)
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
            return $this->builder->whereDate('dob', $value);
        }

        return $this->builder;
    }

    /**
     * Filter by social status.
     *
     * @param string|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function social_status($value)
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
    protected function current_city($value)
    {
        if (!is_null($value) && $value !== '') {
            return $this->builder->where('current_city', $value);
        }

        return $this->builder;
    }
}
