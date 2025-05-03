<?php

namespace App\Http\Filters; // تأكد من المسار الصحيح للـ trait

use Illuminate\Support\Facades\App;
use App\Http\Filters\BaseFilters;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|static filter(BaseFilters $filters = null)
 */
trait Filterable
{
    /**
     * Apply all relevant thread filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Http\Filters\BaseFilters|null $filters
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeFilter($query, BaseFilters $filters = null)
    {
        if (! $filters) {
            $filters = App::make($this->filter, ['builder' => $query]);
        }

        return $filters->apply($query);
    }

    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return request('perPage', parent::getPerPage());
    }
}
