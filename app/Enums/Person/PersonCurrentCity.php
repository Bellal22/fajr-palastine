<?php

namespace App\Enums\Person;

use Illuminate\Support\Facades\Lang;
use Spatie\Enum\Enum;

/**
 * @method static self northGaza()
 * @method static self gaza()
 * @method static self alwsta()
 * @method static self khanYounis()
 * @method static self rafah()
 */
class PersonCurrentCity extends Enum
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
            self::northGaza()->value   => self::northGaza()->getLabelAttribute(),
            self::gaza()->value        => self::gaza()->getLabelAttribute(),
            self::alwsta()->value      => self::alwsta()->getLabelAttribute(),
            self::khanYounis()->value  => self::khanYounis()->getLabelAttribute(),
            self::rafah()->value       => self::rafah()->getLabelAttribute(),
        ];
    }
}