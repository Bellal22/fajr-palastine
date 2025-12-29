<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\OutboundShipmentFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutboundShipment extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = OutboundShipmentFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shipment_number',
        'project_id',
        'sub_warehouse_id',
        'notes',
        'warehouse_keeper_signature',
        'driver_signature',
        'driver_name',
    ];
    // العلاقات
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function subWarehouse()
    {
        return $this->belongsTo(SubWarehouse::class);
    }
    public function items()
    {
        return $this->hasMany(OutboundShipmentItem::class);
    }
}
