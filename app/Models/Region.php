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
     * حساب المساحة الإجمالية للمنطقة (بالكيلومتر المربع وبالأمتار المربعة)
     */
    public function getAreaKm2Attribute()
    {
        $boundaries = $this->boundaries;
        if (empty($boundaries) || !is_array($boundaries) || count($boundaries) < 3) {
            return 0;
        }

        $radius = 6378137.0; // WGS84 Earth radius in meters
        $areaSquareMeters = 0.0;
        $count = count($boundaries);

        for ($i = 0; $i < $count; $i++) {
            $p1 = $boundaries[$i];
            $p2 = $boundaries[($i + 1) % $count];

            $lat1 = deg2rad((float)($p1['lat'] ?? $p1[0] ?? 0));
            $lng1 = deg2rad((float)($p1['lng'] ?? $p1[1] ?? 0));
            $lat2 = deg2rad((float)($p2['lat'] ?? $p2[0] ?? 0));
            $lng2 = deg2rad((float)($p2['lng'] ?? $p2[1] ?? 0));

            $areaSquareMeters += ($lng2 - $lng1) * (2 + sin($lat1) + sin($lat2));
        }

        $areaSquareMeters = abs($areaSquareMeters * ($radius * $radius) / 2.0);
        $km2 = $areaSquareMeters / 1000000.0;

        // إذا كانت المساحة أصغر من 0.01 كم² (مثلاً عدة آلاف من الأمتار المربعة) يتم تقريبها إلى 3 أو 4 منازل أو عرضها بدقة
        if ($km2 >= 1) {
            return round($km2, 2);
        } elseif ($km2 >= 0.01) {
            return round($km2, 3);
        } else {
            return round($km2, 4);
        }
    }

    /**
     * حساب المساحة بالأمتار المربعة (مُنسقة للعرض الدقيق عند الحاجة)
     */
    public function getAreaFormattedAttribute()
    {
        $boundaries = $this->boundaries;
        if (empty($boundaries) || !is_array($boundaries) || count($boundaries) < 3) {
            return '0 م²';
        }

        $radius = 6378137.0;
        $areaSquareMeters = 0.0;
        $count = count($boundaries);

        for ($i = 0; $i < $count; $i++) {
            $p1 = $boundaries[$i];
            $p2 = $boundaries[($i + 1) % $count];

            $lat1 = deg2rad((float)($p1['lat'] ?? $p1[0] ?? 0));
            $lng1 = deg2rad((float)($p1['lng'] ?? $p1[1] ?? 0));
            $lat2 = deg2rad((float)($p2['lat'] ?? $p2[0] ?? 0));
            $lng2 = deg2rad((float)($p2['lng'] ?? $p2[1] ?? 0));

            $areaSquareMeters += ($lng2 - $lng1) * (2 + sin($lat1) + sin($lat2));
        }

        $areaSquareMeters = abs($areaSquareMeters * ($radius * $radius) / 2.0);

        if ($areaSquareMeters >= 1000000) {
            $km2 = round($areaSquareMeters / 1000000.0, 2);
            return number_format($km2, 2) . ' كم² (' . number_format(round($areaSquareMeters)) . ' م²)';
        } else {
            return number_format(round($areaSquareMeters)) . ' م²';
        }
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

    /**
     * Set the center_lat attribute after cleaning/sanitizing.
     */
    public function setCenterLatAttribute($value)
    {
        $this->attributes['center_lat'] = static::cleanCoordinate($value);
    }

    /**
     * Set the center_lng attribute after cleaning/sanitizing.
     */
    public function setCenterLngAttribute($value)
    {
        $this->attributes['center_lng'] = static::cleanCoordinate($value);
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

        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '٧', '٨', '٩'];
        $value = str_replace($persianDigits, $englishDigits, $value);

        $value = str_replace(['٫', ','], '.', $value);

        return trim($value);
    }
}