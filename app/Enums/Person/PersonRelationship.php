<?php

namespace App\Enums\Person;

use Illuminate\Support\Facades\Lang;
use Spatie\Enum\Enum;

/**
 *
 *
 * @method static self father()
 * @method static self mother()
 * @method static self brother()
 * @method static self sister()
 * @method static self husband()
 * @method static self wife()
 * @method static self son()
* @method static self daughter()
 * @method static self others()
 */
class PersonRelationship extends Enum {
    public function getLabelAttribute()
    {
        // استخدم Lang لترجمة القيم من ملف JSON
        return Lang::get($this->value);
    }
}