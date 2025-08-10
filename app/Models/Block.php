<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\BlockFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blocks';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = BlockFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'area_responsible_id',
        'title',
        'phone',
        'people_count',
        'lan',
        'lat',
        'note',
    ];

    protected $casts = [
        'people_count' => 'integer'
    ];

    /**
     * Get the area responsible that owns the block.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function areaResponsible(): BelongsTo
    {
        // تحديد الموديل الصحيح بناءً على البيانات
        return $this->belongsTo(Supervisor::class, 'area_responsible_id', 'id');
    }

    /**
     * Get all people in this block.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function people(): HasMany
    {
        return $this->hasMany(Person::class, 'block_id', 'id');
    }

    public function updatePeopleCount(): int
    {
        try {
            $count = $this->people()->count();
            $this->update(['people_count' => $count]);

            logger()->info('تم تحديث عدد الأفراد للمندوب', [
                'block_id' => $this->id,
                'block_name' => $this->name,
                'old_count' => $this->getOriginal('people_count'),
                'new_count' => $count
            ]);

            return $count;
        } catch (\Exception $e) {
            logger()->error('خطأ في تحديث عدد الأفراد للمندوب', [
                'block_id' => $this->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return $this->people_count ?? 0; // إرجاع القيمة الحالية أو 0
        }
    }

    // حساب العدد مباشرة بدون حفظ
    public function calculatePeopleCount(): int
    {
        try {
            return $this->people()->count();
        } catch (\Exception $e) {
            logger()->error('خطأ في حساب عدد الأفراد للمندوب', [
                'block_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    // Accessor للحصول على العدد المحدث دائماً
    public function getFreshPeopleCountAttribute(): int
    {
        return $this->calculatePeopleCount();
    }

    // Boot method لتحديث العدد عند إنشاء المندوب
    protected static function boot()
    {
        parent::boot();

        static::created(function ($block) {
            try {
                $block->updatePeopleCount();
            } catch (\Exception $e) {
                logger()->error('خطأ في تحديث عدد الأفراد عند إنشاء المندوب', [
                    'block_id' => $block->id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }
}
