<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\ProjectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = ProjectFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the executing entities (Suppliers).
     */
    public function executingEntities()
    {
        return $this->belongsToMany(Supplier::class, 'project_partners')
                    ->wherePivot('type', 'executing')
                    ->withTimestamps();
    }

    /**
     * Get the granting entities (Suppliers).
     */
    public function grantingEntities()
    {
        return $this->belongsToMany(Supplier::class, 'project_partners')
                    ->wherePivot('type', 'granting')
                    ->withTimestamps();
    }

    /**
     * Get the coupon types associated with the project.
     */
    public function couponTypes()
    {
        return $this->belongsToMany(CouponType::class, 'project_coupon_types')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Get the ready packages associated with the project.
     */
    public function readyPackages()
    {
        return $this->morphedByMany(ReadyPackage::class, 'packageable', 'project_packages')
                    ->withTimestamps();
    }

    /**
     * Get the internal packages associated with the project.
     */
    public function internalPackages()
    {
        return $this->morphedByMany(InternalPackage::class, 'packageable', 'project_packages')
                    ->withTimestamps();
    }
    
    public function outboundShipments()
    {
        return $this->hasMany(OutboundShipment::class);
    }
    public function inboundShipments()
    {
        return $this->hasMany(InboundShipment::class);
    }
}
