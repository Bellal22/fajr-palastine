<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\GameWinningFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameWinning extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = GameWinningFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'person_id',
        'coupon_type_id',
        'status',
        'delivered_at',
    ];

    /**
     * Get the beneficiary that won the game.
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Get the coupon type won.
     */
    public function couponType()
    {
        return $this->belongsTo(CouponType::class);
    }

    /**
     * Get the name of the winning (using code as name).
     */
    public function getNameAttribute()
    {
        return $this->code;
    }
}
