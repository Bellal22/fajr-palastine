<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class IncompletePerson extends Model
{
    use HasFactory, Filterable, Selectable;

    protected $table = 'incomplete_people';
    protected $guarded = [];
    protected $casts = ['dob' => 'date', 'is_frozen' => 'boolean'];

    // --- Relationships ---

    /**
     * Get the wife of this person (if this person is a head)
     */
    public function wife()
    {
        return $this->hasOne(IncompletePerson::class, 'relative_id', 'id_num')
            ->where('relationship', 'زوجة');
    }

    /**
     * Get the children/dependents of this person
     */
    public function children()
    {
        return $this->hasMany(IncompletePerson::class, 'relative_id', 'id_num')
            ->where('relationship', '!=', 'زوجة');
    }


    /**
     * Relationship with regular projects
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_beneficiaries', 'incomplete_person_id', 'project_id')
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id')
            ->withTimestamps();
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block_id', 'id');
    }

    public function areaResponsible(): BelongsTo
    {
        return $this->belongsTo(AreaResponsible::class, 'area_responsible_id', 'id');
    }

    // --- Accessors & Helpers ---

    public function getFullName(): string
    {
        $nameParts = [
            $this->first_name ?? '',
            $this->father_name ?? '',
            $this->grandfather_name ?? '',
            $this->family_name ?? ''
        ];
        return trim(implode(' ', array_filter($nameParts))) ?: 'غير مكتمل البيانات';
    }

    public function getFullQuadName(): string
    {
        return $this->getFullName();
    }

    public function getNameAttribute(): string
    {
        return $this->getFullName();
    }

    public function getQuadNameAttribute(): string
    {
        return $this->getFullQuadName();
    }

    /**
     * التحقق من صحة رقم الهوية بناءً على معادلة لونا (Luhn Algorithm)
     */
    public static function isLuhnValid($id): bool
    {
        if (empty($id)) return false;
        $id = preg_replace('/\D/', '', (string) $id);
        if (strlen($id) === 0 || strlen($id) > 9) return false;
        $id = str_pad($id, 9, '0', STR_PAD_LEFT);

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $digit = (int) $id[$i];
            $step = $digit * (($i % 2) + 1);
            if ($step > 9) $step -= 9;
            $sum += $step;
        }

        return ($sum % 10 === 0);
    }

    /**
     * توحيد وتنسيق رقم الجوال ليبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام
     */
    public static function normalizePhone($phone): ?string
    {
        if (empty($phone)) return null;
        $phone = preg_replace('/\D/', '', (string) $phone);

        // إزالة بادئة الدولة إذا وجدت (970 أو 972)
        if (str_starts_with($phone, '970')) {
            $phone = substr($phone, 3);
        } elseif (str_starts_with($phone, '972')) {
            $phone = substr($phone, 3);
        }

        // إذا كان يبدأ بـ 0 أزله مؤقتاً لفحص الطول
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        // إذا كان 9 أرقام ويبدأ بـ 59 أو 56 أضف 0 في البداية
        if (strlen($phone) === 9 && (str_starts_with($phone, '59') || str_starts_with($phone, '56'))) {
            return '0' . $phone;
        }

        return !empty($phone) ? '0' . $phone : null;
    }

    /**
     * التحقق من أن رقم الجوال يبدأ بـ 059 أو 056 ومتبوعاً بـ 7 أرقام (10 أرقام إجمالاً)
     */
    public static function isPhoneValid($phone): bool
    {
        if (empty($phone)) return false;
        $normalized = self::normalizePhone($phone);
        return (bool) preg_match('/^(059|056)\d{7}$/', $normalized ?? '');
    }

    /**
     * فحص هل هوية السجل الحالي صحيحة وفق معادلة لونا
     */
    public function isValidId(): bool
    {
        return self::isLuhnValid($this->id_num);
    }

    /**
     * فحص هل رقم جوال السجل الحالي صالح
     */
    public function isValidPhone(): bool
    {
        return self::isPhoneValid($this->phone);
    }

    /**
     * فحص هل السجل مدرج في قائمة الحظر (البلوكات)
     */
    public function isBanned(): bool
    {
        return !empty($this->id_num) && BanList::where('id_num', $this->id_num)->exists();
    }

    /**
     * فحص هل السجل مسجل كموظف
     */
    public function isEmployee(): bool
    {
        return !empty($this->id_num) && Employee::where('id_num', $this->id_num)->exists();
    }

    /**
     * فحص هل السجل مسجل كمستفيد في جدول الأفراد
     */
    public function isBeneficiary(): bool
    {
        return !empty($this->id_num) && Person::where('id_num', $this->id_num)->exists();
    }
}

