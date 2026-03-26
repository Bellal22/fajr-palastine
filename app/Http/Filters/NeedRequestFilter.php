<?php

namespace App\Http\Filters;

class NeedRequestFilter extends BaseFilters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = [
        'name',
        'project_id',
        'supervisor_id',
        'status',
        'selected_id',
    ];

    /**
     * Filter the query by a given supervisor id.
     *
     * @param int|string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function supervisorId($value)
    {
        if ($value) {
            return $this->builder->where('supervisor_id', $value);
        }

        return $this->builder;
    }

    /**
     * Filter the query by a given status.
     *
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function status($value)
    {
        if ($value) {
            return $this->builder->where('status', $value);
        }

        return $this->builder;
    }

    /**
     * Filter the query by a given project id.
     *
     * @param int|string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function projectId($value)
    {
        if ($value) {
            return $this->builder->where('project_id', $value);
        }

        return $this->builder;
    }

    /**
     * Filter the query by a given name.
     *
     * @param string|int $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function name($value)
    {
        if ($value) {
            return $this->builder->where('name', 'like', "%$value%");
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
}
