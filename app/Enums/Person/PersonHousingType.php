<?php

namespace App\Enums\Person;

use Illuminate\Support\Facades\Lang;
use Spatie\Enum\Enum;

/**
 * @method static self tent()
 * @method static self zinc()
 * @method static self concrete()
 */
class PersonHousingType extends Enum
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
            self::tent()->value     => self::tent()->getLabelAttribute(),
            self::zinc()->value     => self::zinc()->getLabelAttribute(),
            self::concrete()->value => self::concrete()->getLabelAttribute(),
        ];
    }
}