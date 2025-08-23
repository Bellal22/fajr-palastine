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
        return $this->belongsTo(AreaResponsible::class, 'area_responsible_id', 'id');
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

    protected static function boot()
    {
        parent::boot();

        // عند إنشاء شخص جديد
        static::created(function ($person) {
            if ($person->block_id) {
                // تحديث عدد الأشخاص في المندوب
                $person->updateBlockPeopleCount($person->block_id);
                // تحديث عدد الأشخاص في مسؤول المنطقة
                $person->updateAreaResponsiblePeopleCount();
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
                    // تحديث مسؤول المنطقة للمندوب القديم
                    $oldBlock = Block::find($oldBlockId);
                    if ($oldBlock && $oldBlock->area_responsible_id) {
                        $person->updateAreaResponsiblePeopleCountById($oldBlock->area_responsible_id);
                    }
                }

                // تحديث المندوب الجديد
                if ($newBlockId) {
                    $person->updateBlockPeopleCount($newBlockId);
                    // تحديث مسؤول المنطقة للمندوب الجديد
                    $person->updateAreaResponsiblePeopleCount();
                }
            }
        });

        // عند حذف شخص
        static::deleted(function ($person) {
            if ($person->block_id) {
                $person->updateBlockPeopleCount($person->block_id);
                $person->updateAreaResponsiblePeopleCount();
            }
        });

    }

    // دالة لتحديث عدد الأفراد في مسؤول المنطقة
    protected function updateAreaResponsiblePeopleCount()
    {
        if ($this->block && $this->block->area_responsible_id) {
            $this->updateAreaResponsiblePeopleCountById($this->block->area_responsible_id);
        }
    }

    protected function updateAreaResponsiblePeopleCountById($areaResponsibleId)
    {
        try {
            $areaResponsible = AreaResponsible::find($areaResponsibleId);
            if ($areaResponsible) {
                $areaResponsible->updatePeopleCount();
            }
        } catch (\Exception $e) {
            logger()->error('خطأ في تحديث عدد مسؤول المنطقة من Person', [
                'person_id' => $this->id,
                'area_responsible_id' => $areaResponsibleId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getWifeId()
    {
        $wife = $this->relatives()
            ->where('relationship', 'wife')
            ->first();

        if ($wife) {
            $wifePerson = Person::where('relative_id', $wife->relative_id)
                ->where('id', '!=', $this->id)
                ->first();
            return $wifePerson ? $wifePerson->id_num : '';
        }

        return '';
    }

    public function getWifeName()
    {
        $wife = $this->relatives()
            ->where('relationship', 'wife')
            ->first();

        if ($wife) {
            $wifePerson = Person::where('relative_id', $wife->relative_id)
                ->where('id', '!=', $this->id)
                ->first();

            if ($wifePerson) {
                return trim(
                    ($wifePerson->first_name ?? '') . ' ' .
                    ($wifePerson->father_name ?? '') . ' ' .
                    ($wifePerson->grandfather_name ?? '') . ' ' .
                    ($wifePerson->family_name ?? '')
                );
            }
        }

        return '';
    }

    public function getChildrenUnder3Count()
    {
        if (!$this->dob || !$this->relative_id) {
            return 0;
        }

        return Person::where('relative_id', $this->relative_id)
            ->where('id', '!=', $this->id)
            ->where('dob', '>', now()->subYears(3))
            ->count();
    }

    public function getHomeStatus()
    {
        // تحويل home_status لقيم API
        switch ($this->home_status) {
            case 'excellent':
            case 'notAffected':
                return '3';
            case 'good':
            case 'partial':
                return '2';
            case 'bad':
            case 'total':
                return '1';
            default:
                return 'غير محدد';
        }
    }
}
