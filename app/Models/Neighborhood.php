<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\NeighborhoodFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Neighborhood extends Model
{

    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = NeighborhoodFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * العلاقة مع مسؤولي المناطق (Many-to-Many)
     */
    public function areaResponsibles()
    {
        return $this->belongsToMany(AreaResponsible::class, 'area_responsible_neighborhood');
    }

    /**
     * العلاقة مع المدينة
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}