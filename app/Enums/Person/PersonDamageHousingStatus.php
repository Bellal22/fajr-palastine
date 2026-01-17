<?php

namespace App\Enums\Person;

use Illuminate\Support\Facades\Lang;
use Spatie\Enum\Enum;

/**
 * @method static self total()
 * @method static self partial()
 * @method static self notAffected()
 */
class PersonDamageHousingStatus extends Enum
{
    /**
     * إرجاع الترجمة من ملف اللغة
     */
    public function getLabelAttribute()
    {
        return Lang::get($this->value);
    }

    /**
     * دالة مخصصة لإرجاع خيارات القائمة المنسدلة
     * تستخدم داخل ملف الـ Blade في BsForm::options()
     */
    public static function options(): array
    {
        return [
            self::total()->value       => self::total()->getLabelAttribute(),
            self::partial()->value     => self::partial()->getLabelAttribute(),
            self::notAffected()->value => self::notAffected()->getLabelAttribute(),
        ];
    }
}