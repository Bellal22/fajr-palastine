<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\RegionFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = RegionFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'color',
        'area_responsible_id',
        'boundaries',
        'center_lat',
        'center_lng',
        'is_active'
    ];

    protected $casts = [
        'boundaries' => 'array', // تحويل JSON لـ array
        'center_lat' => 'decimal:8',
        'center_lng' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    /**
     * العلاقة مع مسؤول المنطقة
     */
    public function areaResponsible()
    {
        return $this->belongsTo(AreaResponsible::class, 'area_responsible_id');
    }

    /**
     * العلاقة مع اللوكيشنات
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * حساب عدد اللوكيشنات النشطة
     */
    public function getActiveLocationsCountAttribute()
    {
        return $this->locations()->where('is_active', true)->count();
    }

    /**
     * حساب إجمالي عدد الأشخاص في المنطقة
     */
    public function getTotalPeopleCountAttribute()
    {
        return $this->locations()->sum('people_count');
    }
}