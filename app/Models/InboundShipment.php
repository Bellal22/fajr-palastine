<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\InboundShipmentFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboundShipment extends Model
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
    protected $filter = InboundShipmentFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplier_id',
        'name',
        'description',
        'type',
        'weight',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'boolean',
        'weight' => 'float',
        'quantity' => 'integer',
    ];

    /**
     * Items relation (many-to-many).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'inbound_shipment_item')
            ->using(InboundShipmentItem::class)
            ->withTimestamps();
    }
}
