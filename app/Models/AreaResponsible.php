<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\AreaResponsibleFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AreaResponsible extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = AreaResponsibleFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'people_count',
        'phone',
        'address',
    ];

    protected $casts = [
        'people_count' => 'integer'
    ];

    // العلاقة مع الأشخاص
    public function people()
    {
        return $this->hasMany(Person::class, 'area_responsible_id', 'id');
    }

    // العلاقة مع المندوبين
    public function blocks()
    {
        return $this->hasMany(Block::class, 'area_responsible_id', 'id');
    }

    /**
     * حساب عدد الأفراد تلقائياً وحفظه
     */
    public function updatePeopleCount(): int
    {
        try {
            $count = $this->people()->count();
            $this->update(['people_count' => $count]);

            logger()->info('تم تحديث عدد الأفراد لمسؤول المنطقة', [
                'area_responsible_id' => $this->id,
                'area_responsible_name' => $this->name,
                'old_count' => $this->getOriginal('people_count'),
                'new_count' => $count
            ]);

            return $count;
        } catch (\Exception $e) {
            logger()->error('خطأ في تحديث عدد الأفراد لمسؤول المنطقة', [
                'area_responsible_id' => $this->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return $this->people_count; // إرجاع القيمة الحالية
        }
    }

    /**
     * حساب العدد مباشرة بدون حفظ
     */
    public function calculatePeopleCount(): int
    {
        return $this->people()->count();
    }

    /**
     * Accessor للحصول على العدد المحدث دائماً
     */
    public function getFreshPeopleCountAttribute(): int
    {
        return $this->calculatePeopleCount();
    }

    /**
     * Boot method لتحديث العدد عند إنشاء مسؤول المنطقة
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($areaResponsible) {
            $areaResponsible->updatePeopleCount();
        });
    }

}