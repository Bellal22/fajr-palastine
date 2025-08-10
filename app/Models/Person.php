<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Http\Filters\PersonFilter;
use App\Support\Traits\Selectable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    protected $table = 'persons';

    protected $guarded = [];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = PersonFilter::class;

    /**
     * علاقة "شخص" إلى "أفراد الأسرة" (علاقة "hasMany")
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(Person::class, 'relative_id', 'id_num');
    }

    /**
     * User relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Area responsible relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function areaResponsible(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class, 'area_responsible_id', 'id');
    }

    /**
     * Block relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        // عند إنشاء شخص جديد
        static::created(function ($person) {
            if ($person->block_id) {
                $person->updateBlockPeopleCount($person->block_id);
            }
        });

        // عند تحديث شخص
        static::updated(function ($person) {
            // إذا تم تغيير المندوب
            if ($person->isDirty('block_id')) {
                $oldBlockId = $person->getOriginal('block_id');
                $newBlockId = $person->block_id;

                // تحديث المندوب القديم
                if ($oldBlockId) {
                    $person->updateBlockPeopleCount($oldBlockId);
                }

                // تحديث المندوب الجديد
                if ($newBlockId) {
                    $person->updateBlockPeopleCount($newBlockId);
                }
            }
        });

        // عند حذف شخص
        static::deleted(function ($person) {
            if ($person->block_id) {
                $person->updateBlockPeopleCount($person->block_id);
            }
        });

        // تم حذف restored() event لأنه غير مطلوب بدون SoftDeletes
    }

    /**
     * دالة لتحديث عدد الأفراد في المندوب
     *
     * @param int $blockId
     * @return void
     */
    protected function updateBlockPeopleCount($blockId): void
    {
        try {
            $block = Block::find($blockId);
            if ($block) {
                $block->updatePeopleCount();
            }
        } catch (\Exception $e) {
            logger()->error('خطأ في تحديث عدد المندوب من Person', [
                'person_id' => $this->id,
                'block_id' => $blockId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}
