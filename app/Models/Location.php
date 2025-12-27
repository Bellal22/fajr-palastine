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
        'block_id',
        'latitude',
        'longitude',
        'type',
        'icon_color',
        'address',
        'phone',
        'is_active'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
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
     * العلاقة مع البلوك/المندوب
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
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

    /**
     * الحصول على اسم نوع اللوكيشن بالعربي
     */
    public function getTypeNameAttribute()
    {
        $types = [
            'house' => 'منزل',
            'shelter' => 'ملجأ',
            'center' => 'مركز',
            'other' => 'أخرى'
        ];

        return $types[$this->type] ?? 'أخرى';
    }
}
