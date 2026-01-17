<?php

namespace App\Enums\Person;

use Illuminate\Support\Facades\Lang;
use Spatie\Enum\Enum;

/**
 * @method static self single()
 * @method static self married()
 * @method static self polygamous()
 * @method static self divorced()
 * @method static self widowed()
 */
class PersonSocialStatus extends Enum {
    public function getLabelAttribute()
    {
        // استخدم Lang لترجمة القيم من ملف JSON
        return Lang::get($this->value);
    }

    /**
     * دالة مخصصة لإرجاع خيارات القائمة المنسدلة
     * تستخدم داخل ملف الـ Blade في BsForm::options()
     */
    public static function options(): array
    {
        return [
            self::single()->value       => self::single()->getLabelAttribute(),
            self::married()->value      => self::married()->getLabelAttribute(),
            self::polygamous()->value   => self::polygamous()->getLabelAttribute(),
            self::divorced()->value     => self::divorced()->getLabelAttribute(),
            self::widowed()->value      => self::widowed()->getLabelAttribute(),
        ];
    }
}
