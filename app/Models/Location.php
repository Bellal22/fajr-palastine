<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\LocationFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;
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
     * الحصول على مسؤول المنطقة (من المنطقة أو البلوك)
     */
    public function getAreaResponsibleAttribute()
    {
        return $this->region?->areaResponsible ?? $this->block?->areaResponsible;
    }

    /**
     * الحصول على معرف مسؤول المنطقة
     */
    public function getAreaResponsibleIdAttribute()
    {
        return $this->region?->area_responsible_id ?? $this->block?->area_responsible_id;
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

    /**
     * Set the latitude attribute after cleaning/sanitizing.
     */
    public function setLatitudeAttribute($value)
    {
        $this->attributes['latitude'] = static::cleanCoordinate($value);
    }

    /**
     * Set the longitude attribute after cleaning/sanitizing.
     */
    public function setLongitudeAttribute($value)
    {
        $this->attributes['longitude'] = static::cleanCoordinate($value);
    }

    /**
     * Clean and sanitize coordinate value (decimal separator, Arabic numbers, etc.)
     */
    public static function cleanCoordinate($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $value = str_replace($arabicDigits, $englishDigits, $value);

        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $value = str_replace($persianDigits, $englishDigits, $value);

        $value = str_replace(['٫', ','], '.', $value);

        return trim($value);
    }
}

