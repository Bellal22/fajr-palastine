<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Http\Filters\PersonFilter;
use App\Support\Traits\Selectable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Person extends Model
{
    use HasFactory, Filterable, Selectable;

    protected $table = 'persons';
    protected $guarded = [];
    protected $casts = ['dob' => 'date', 'is_frozen' => 'boolean'];
    protected $filter = PersonFilter::class;

    // --- Relationships ---

    public function familyMembers(): HasMany
    {
        return $this->hasMany(Person::class, 'relative_id', 'id_num');
    }

    public function familyHead(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'relative_id', 'id_num');
    }

    public function relatives(): HasMany
    {
        return $this->hasMany(Person::class, 'relative_id', 'relative_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function areaResponsible(): BelongsTo
    {
        return $this->belongsTo(AreaResponsible::class, 'area_responsible_id', 'id');
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id', 'id');
    }

    public function wife(): HasOne
    {
        return $this->hasOne(Person::class, 'relative_id', 'id_num')
            ->where('relationship', 'زوجة');
    }

    public function spouse(): HasOne
    {
        return $this->hasOne(Person::class, 'relative_id', 'id_num')
            ->whereIn('relationship', ['زوجة', 'زوج']);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_beneficiaries')
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id')
            ->withTimestamps();
    }

    // --- Accessors & Helpers ---

    public function getWifeId()
    {
        return $this->wife ? $this->wife->id_num : '';
    }

    public function getWifeName()
    {
        $spouse = $this->wife ?: $this->spouse;
        if (!$spouse) {
            return '';
        }
        $nameParts = [
            $spouse->first_name ?? '',
            $spouse->father_name ?? '',
            $spouse->grandfather_name ?? '',
            $spouse->family_name ?? ''
        ];
        return trim(implode(' ', array_filter($nameParts)));
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

    public function getFullName(): string
    {
        return trim("{$this->first_name} {$this->family_name}");
    }

    public function getFullQuadName(): string
    {
        $nameParts = [
            $this->first_name ?? '',
            $this->father_name ?? '',
            $this->grandfather_name ?? '',
            $this->family_name ?? ''
        ];
        return trim(implode(' ', array_filter($nameParts)));
    }

    public function getNameAttribute(): string
    {
        return $this->getFullName();
    }

    public function getQuadNameAttribute(): string
    {
        return $this->getFullQuadName();
    }

    // --- Scopes ---

    public function scopeFamilyHead($query)
    {
        return $query->whereNull('relative_id');
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('area_responsible_id')
            ->whereNotNull('block_id');
    }

    public function scopeUnapproved($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('area_responsible_id')
                ->orWhereNull('block_id');
        });
    }

    public function scopeDependents($query)
    {
        return $query->whereNotNull('relative_id');
    }

    // --- Model Events ---

    protected static function boot()
    {
        parent::boot();

        static::created(function ($person) {
            if ($person->block_id) {
                $person->updateBlockPeopleCount($person->block_id);
                $person->updateAreaResponsiblePeopleCount();
            }
            // مسح Cache بشكل انتقائي
            Cache::forget('family_head_stats');
            Cache::forget('all_medical_stats');
        });

        static::updated(function ($person) {
            if ($person->isDirty('block_id')) {
                $oldBlockId = $person->getOriginal('block_id');
                $newBlockId = $person->block_id;

                if ($oldBlockId) {
                    $person->updateBlockPeopleCount($oldBlockId);
                    $oldBlock = Block::find($oldBlockId);
                    if ($oldBlock && $oldBlock->area_responsible_id) {
                        $person->updateAreaResponsiblePeopleCountById($oldBlock->area_responsible_id);
                    }
                }

                if ($newBlockId) {
                    $person->updateBlockPeopleCount($newBlockId);
                    $person->updateAreaResponsiblePeopleCount();
                }
            }

            // مسح Cache بس عند تغييرات مهمة
            if ($person->isDirty(['area_responsible_id', 'block_id', 'family_name', 'condition_description'])) {
                Cache::forget('family_head_stats');
                Cache::forget('all_medical_stats');
            }
        });

        static::deleted(function ($person) {
            if ($person->block_id) {
                $person->updateBlockPeopleCount($person->block_id);
                $person->updateAreaResponsiblePeopleCount();
            }
            Cache::forget('family_head_stats');
            Cache::forget('all_medical_stats');
        });
    }

    // --- Private Helper Methods ---

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

    // --- Static Methods for Statistics ---

    public static function getFamilyHeadStats()
    {
        return Cache::remember('family_head_stats', 7200, function () {
            return DB::table('persons')
                ->whereNull('relative_id')
                ->selectRaw('
                    COUNT(*) as total_count,
                    SUM(CASE WHEN area_responsible_id IS NOT NULL AND block_id IS NOT NULL THEN 1 ELSE 0 END) as approved_count,
                    SUM(CASE WHEN area_responsible_id IS NULL OR block_id IS NULL THEN 1 ELSE 0 END) as unapproved_count
                ')
                ->first();
        });
    }

    public static function getMonthlyRegistrationStats($year = null)
    {
        $year = $year ?? now()->year;
        return Cache::remember("monthly_stats_{$year}", 7200, function () use ($year) {
            $registeredMonthly = DB::table('persons')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', $year)
                ->groupByRaw('MONTH(created_at)')
                ->pluck('count', 'month');

            $syncedMonthly = DB::table('persons')
                ->selectRaw('MONTH(api_synced_at) as month, COUNT(*) as count')
                ->whereNotNull('api_synced_at')
                ->whereYear('api_synced_at', $year)
                ->groupByRaw('MONTH(api_synced_at)')
                ->pluck('count', 'month');

            $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
            $registeredData = [];
            $syncedData = [];
            for ($i = 1; $i <= 12; $i++) {
                $registeredData[$months[$i - 1]] = $registeredMonthly[$i] ?? 0;
                $syncedData[$months[$i - 1]] = $syncedMonthly[$i] ?? 0;
            }

            return ['registered' => $registeredData, 'synced' => $syncedData, 'months' => $months];
        });
    }

    public static function getSocialStatusStats()
    {
        return Cache::remember('social_status_stats', 7200, function () {
            return DB::table('persons')
                ->whereNull('relative_id')
                ->selectRaw('
                    social_status,
                    SUM(CASE WHEN area_responsible_id IS NOT NULL AND block_id IS NOT NULL THEN 1 ELSE 0 END) as approved_count,
                    SUM(CASE WHEN area_responsible_id IS NULL OR block_id IS NULL THEN 1 ELSE 0 END) as unapproved_count
                ')
                ->groupBy('social_status')
                ->get();
        });
    }

    public static function getGenderStats()
    {
        return Cache::remember('gender_stats', 7200, function () {
            $approved = DB::table('persons')
                ->whereNull('relative_id')
                ->whereNotNull('area_responsible_id')
                ->whereNotNull('block_id')
                ->selectRaw('COALESCE(gender, "غير محدد") as gender, COUNT(*) as count')
                ->groupBy('gender')
                ->pluck('count', 'gender');

            $unapproved = DB::table('persons')
                ->whereNull('relative_id')
                ->where(function ($q) {
                    $q->whereNull('area_responsible_id')->orWhereNull('block_id');
                })
                ->selectRaw('COALESCE(gender, "غير محدد") as gender, COUNT(*) as count')
                ->groupBy('gender')
                ->pluck('count', 'gender');

            return compact('approved', 'unapproved');
        });
    }

    public static function getAreaStats()
    {
        return Cache::remember('area_stats', 7200, function () {
            $stats = DB::table('persons')
                ->whereNull('relative_id')
                ->whereNotNull('area_responsible_id')
                ->selectRaw('
                area_responsible_id,
                COUNT(*) as total_persons,
                SUM(CASE WHEN block_id IS NOT NULL THEN 1 ELSE 0 END) as approved_persons,
                SUM(CASE WHEN api_synced_at IS NOT NULL THEN 1 ELSE 0 END) as synced_persons
            ')
                ->groupBy('area_responsible_id')
                ->get();

            return $stats->map(function ($item) {
                $area = \App\Models\AreaResponsible::find($item->area_responsible_id);

                return [
                    'id'               => $item->area_responsible_id,
                    'responsible_id'   => $item->area_responsible_id, // لهذا المفتاح تستخدمه في الراوت
                    'name'             => $area->name ?? 'غير محدد',
                    'total'            => $item->total_persons,
                    'approved'         => $item->approved_persons,
                    'synced'           => $item->synced_persons,
                    'approved_percent' => $item->total_persons > 0
                        ? round(($item->approved_persons / $item->total_persons) * 100)
                        : 0,
                    'synced_percent'   => $item->total_persons > 0
                        ? round(($item->synced_persons / $item->total_persons) * 100)
                        : 0,
                ];
            });
        });
    }

    public static function getChildrenStats()
    {
        return Cache::remember('children_stats', 7200, function () {
            $today = now()->toDateString();
            return DB::table('persons as children')
                ->join('persons as parents', 'children.relative_id', '=', 'parents.id_num')
                ->whereNotNull('children.relative_id')
                ->whereNull('parents.relative_id')
                ->whereNotNull('parents.area_responsible_id')
                ->whereNotNull('parents.block_id')
                ->whereNotIn('children.relationship', ['زوجة', 'أب', 'أم'])
                ->selectRaw("
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, children.dob, ?) <= 1 THEN 1 ELSE 0 END) as under_1,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, children.dob, ?) < 3 THEN 1 ELSE 0 END) as under_3
                ", [$today, $today])
                ->first();
        });
    }

    /**
     * جميع الإحصائيات الطبية - استعلام SQL واحد فقط (الأسرع)
     */
    public static function getAllMedicalStats()
    {
        // سنبقي الكاش لأنه الحل الأفضل للسرعة بعد أول تحميل
        return Cache::remember('all_medical_stats', 7200, function () {

            // كويري محسن يجمع الفئات في استعلام فرعي واحد باستخدام REGEXP (أسرع من LIKE المتكرر)
            $result = DB::selectOne("
            SELECT
                SUM(CASE WHEN is_pregnant = 1 AND is_approved = 1 THEN 1 ELSE 0 END) as pregnant_approved,
                SUM(CASE WHEN is_pregnant = 1 AND is_approved = 0 THEN 1 ELSE 0 END) as pregnant_unapproved,
                SUM(CASE WHEN is_nursing = 1 AND is_approved = 1 THEN 1 ELSE 0 END) as nursing_approved,
                SUM(CASE WHEN is_nursing = 1 AND is_approved = 0 THEN 1 ELSE 0 END) as nursing_unapproved,
                SUM(CASE WHEN is_cancer = 1 AND is_approved = 1 THEN 1 ELSE 0 END) as cancer_approved,
                SUM(CASE WHEN is_cancer = 1 AND is_approved = 0 THEN 1 ELSE 0 END) as cancer_unapproved,
                SUM(CASE WHEN is_injured = 1 AND is_approved = 1 THEN 1 ELSE 0 END) as injured_approved,
                SUM(CASE WHEN is_injured = 1 AND is_approved = 0 THEN 1 ELSE 0 END) as injured_unapproved,
                SUM(CASE WHEN is_kidney = 1 AND is_approved = 1 THEN 1 ELSE 0 END) as kidney_approved,
                SUM(CASE WHEN is_kidney = 1 AND is_approved = 0 THEN 1 ELSE 0 END) as kidney_unapproved,
                SUM(CASE WHEN is_incontinence = 1 AND is_approved = 1 THEN 1 ELSE 0 END) as incontinence_approved,
                SUM(CASE WHEN is_incontinence = 1 AND is_approved = 0 THEN 1 ELSE 0 END) as incontinence_unapproved,
                SUM(CASE WHEN is_disability = 1 AND is_approved = 1 THEN 1 ELSE 0 END) as disability_approved,
                SUM(CASE WHEN is_disability = 1 AND is_approved = 0 THEN 1 ELSE 0 END) as disability_unapproved
            FROM (
                SELECT
                    -- تحديد حالة الاعتماد (نفس منطقك الأصلي تماماً)
                    (CASE
                        WHEN (p.relative_id IS NULL AND p.area_responsible_id IS NOT NULL AND p.block_id IS NOT NULL)
                             OR (p.relative_id IS NOT NULL AND parent.id_num IS NOT NULL AND parent.area_responsible_id IS NOT NULL AND parent.block_id IS NOT NULL)
                        THEN 1 ELSE 0
                    END) as is_approved,

                    -- الفئات الطبية (تطابق الكلمات بنسبة 100%)
                    (p.family_name LIKE '%حامل%' OR p.condition_description LIKE '%حامل%') as is_pregnant,
                    (p.family_name LIKE '%مرضع%' OR p.condition_description LIKE '%مرضع%') as is_nursing,
                    (p.family_name REGEXP 'سرطان|كانسر|كنسر|كيماوي|ورم|أورام|اورام' OR p.condition_description REGEXP 'سرطان|كانسر|كنسر|كيماوي|ورم|أورام|اورام') as is_cancer,
                    (p.family_name REGEXP 'مصاب|حرب|جريج|بلاتين|إصابة|اصابة|بتر|قطع|بلتين|مصب' OR p.condition_description REGEXP 'مصاب|حرب|جريج|بلاتين|إصابة|اصابة|بتر|قطع|بلتين|مصب') as is_injured,
                    (p.family_name REGEXP 'غسيل كلى|فشل كلوي|مريض كلى|كلا|غسل كلى|غسل كلا|فشل كلى|فشل كلا|كلى' OR p.condition_description REGEXP 'غسيل كلى|فشل كلوي|مريض كلى|كلا|غسل كلى|غسل كلا|فشل كلى|فشل كلا|كلى') as is_kidney,
                    (p.family_name REGEXP 'تبول لا إرادي|بامبرز|بمبرز|مسنين' OR p.condition_description REGEXP 'تبول لا إرادي|بامبرز|بمبرز|مسنين') as is_incontinence,
                    (p.family_name REGEXP 'معاق|توحد|كفيف|ضمور|إعاقة|اعاقة|حركية|حركيه|سمعية|سمعيه|بصرية|بصريه|عقلية|عقليه|ذهنية|ذهنيه|أصم|أعمى|شلل|متلازمة|داون|طيف|صمم|عمى' OR p.condition_description REGEXP 'معاق|توحد|كفيف|ضمور|إعاقة|اعاقة|حركية|حركيه|سمعية|سمعيه|بصرية|بصريه|عقلية|عقليه|ذهنية|ذهنيه|أصم|أعمى|شلل|متلازمة|داون|طيف|صمم|عمى') as is_disability

                FROM persons p
                LEFT JOIN persons parent ON p.relative_id = parent.id_num AND parent.relative_id IS NULL
                -- هذا الفلتر يقلل وقت البحث لأنه يتجاهل السجلات التي لا تحتوي على أي وصف طبي
                WHERE (p.family_name IS NOT NULL OR p.condition_description IS NOT NULL)
            ) as subquery
        ");

            return (object) [
                'pregnant_approved' => (int) ($result->pregnant_approved ?? 0),
                'pregnant_unapproved' => (int) ($result->pregnant_unapproved ?? 0),
                'nursing_approved' => (int) ($result->nursing_approved ?? 0),
                'nursing_unapproved' => (int) ($result->nursing_unapproved ?? 0),
                'cancer_approved' => (int) ($result->cancer_approved ?? 0),
                'cancer_unapproved' => (int) ($result->cancer_unapproved ?? 0),
                'injured_approved' => (int) ($result->injured_approved ?? 0),
                'injured_unapproved' => (int) ($result->injured_unapproved ?? 0),
                'kidney_approved' => (int) ($result->kidney_approved ?? 0),
                'kidney_unapproved' => (int) ($result->kidney_unapproved ?? 0),
                'incontinence_approved' => (int) ($result->incontinence_approved ?? 0),
                'incontinence_unapproved' => (int) ($result->incontinence_unapproved ?? 0),
                'disability_approved' => (int) ($result->disability_approved ?? 0),
                'disability_unapproved' => (int) ($result->disability_unapproved ?? 0),
            ];
        });
    }

    public static function clearAllCache()
    {
        Cache::forget('family_head_stats');
        Cache::forget('social_status_stats');
        Cache::forget('gender_stats');
        Cache::forget('area_stats');
        Cache::forget('children_stats');
        Cache::forget('all_medical_stats');

        for ($year = now()->year - 1; $year <= now()->year + 1; $year++) {
            Cache::forget("monthly_stats_{$year}");
        }
    }
}
