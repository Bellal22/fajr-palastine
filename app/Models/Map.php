<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\MapFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Map extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = MapFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'title',
        'description',
        'default_lat',
        'default_lng',
        'default_zoom',
        'is_active'
    ];

    protected $casts = [
        'default_lat' => 'decimal:8',
        'default_lng' => 'decimal:8',
        'default_zoom' => 'integer',
        'is_active' => 'boolean'
    ];
}