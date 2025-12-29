<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectCoupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'name',
        'coupon_type_id',
        'packageable_type',
        'packageable_id',
        'quantity',
    ];

    /**
     * Get the project that owns the coupon.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the coupon type.
     */
    public function couponType()
    {
        return $this->belongsTo(CouponType::class);
    }

    /**
     * Get the parent packageable model (ReadyPackage or InternalPackage).
     */
    public function packageable()
    {
        return $this->morphTo();
    }
}
