<?php

namespace App\Enums\Person;

use Illuminate\Support\Facades\Lang;
use Spatie\Enum\Enum;

/**
 * @method static self northGaza()
 * @method static self gaza()
 * @method static self zahra()
 * @method static self maghraqa()
 * @method static self breij()
 * @method static self nusairat()
 * @method static self maghazi()
 * @method static self zawaida()
 * @method static self deirBalh()
 * @method static self khanYounis()
 * @method static self rafah()
 */
class PersonCity extends Enum
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
            self::zahra()->value       => self::zahra()->getLabelAttribute(),
            self::maghraqa()->value    => self::maghraqa()->getLabelAttribute(),
            self::breij()->value       => self::breij()->getLabelAttribute(),
            self::nusairat()->value    => self::nusairat()->getLabelAttribute(),
            self::maghazi()->value     => self::maghazi()->getLabelAttribute(),
            self::zawaida()->value     => self::zawaida()->getLabelAttribute(),
            self::deirBalh()->value    => self::deirBalh()->getLabelAttribute(),
            self::khanYounis()->value  => self::khanYounis()->getLabelAttribute(),
            self::rafah()->value       => self::rafah()->getLabelAttribute(),
        ];
    }
}