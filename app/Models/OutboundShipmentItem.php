<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\OutboundShipmentItemFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutboundShipmentItem extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = OutboundShipmentItemFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'outbound_shipment_id',
        'shippable_id',
        'shippable_type',
        'quantity',
        'weight',
    ];
    protected $casts = [
        'quantity' => 'integer',
        'weight' => 'float',
    ];
    public function outboundShipment()
    {
        return $this->belongsTo(OutboundShipment::class);
    }
    // Polymorphic relation
    public function shippable()
    {
        return $this->morphTo();
    }
}