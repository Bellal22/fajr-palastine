<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\ProjectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;
    use SoftDeletes;

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
     * Check if project is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Check if project can accept beneficiaries.
     */
    public function canAcceptBeneficiaries(): bool
    {
        return !$this->isCompleted() && !$this->isExpired();
    }

    /**
     * Auto-update status if expired.
     */
    public function checkAndUpdateStatus()
    {
        if ($this->isExpired() && !$this->isCompleted()) {
            $this->update(['status' => 'completed']);
        }
    }
    /**
     * Get the executing entities (Suppliers).
     */
    public function executingEntities()
    {
        return $this->belongsToMany(Supplier::class, 'project_partners')
            ->wherePivot('type', 'executing')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Get the granting entities (Suppliers).
     */
    public function grantingEntities()
    {
        return $this->belongsToMany(Supplier::class, 'project_partners')
            ->wherePivot('type', 'granting')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Get all partners (both granting and executing).
     */
    public function partners()
    {
        return $this->belongsToMany(Supplier::class, 'project_partners')
            ->withPivot('type')
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
     * Get the projects that conflict with this project.
     */
    public function conflicts()
    {
        return $this->belongsToMany(Project::class, 'project_conflicts', 'project_id', 'conflict_id')
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

    /**
     * Get all packages (both ready and internal).
     */
    public function allPackages()
    {
        return $this->readyPackages->merge($this->internalPackages);
    }

    /**
     * Get the outbound shipments for the project.
     */
    public function outboundShipments()
    {
        return $this->hasMany(OutboundShipment::class);
    }

    /**
     * Get the inbound shipments for the project.
     */
    public function inboundShipments()
    {
        return $this->hasMany(InboundShipment::class);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if project is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if project is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function beneficiaries()
    {
        return $this->belongsToMany(Person::class, 'project_beneficiaries')
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id')
            ->withTimestamps();
    }

    public function needRequestProject()
    {
        return $this->hasOne(NeedRequestProject::class);
    }

    public function needRequests()
    {
        return $this->hasMany(NeedRequest::class);
    }
}
