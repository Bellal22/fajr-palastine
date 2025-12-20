<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\LocationFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = LocationFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'region_id',
        'latitude',
        'longitude',
        'type',
        'icon_color',
        'address',
        'phone',
        'people_count',
        'person_id',
        'is_active'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'people_count' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * العلاقة مع المنطقة
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * العلاقة مع الشخص (اختياري)
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * الحصول على الإحداثيات كـ array
     */
    public function getCoordinatesAttribute()
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude
        ];
    }
}