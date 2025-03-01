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
}
