<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InboundShipmentItem extends Pivot
{
    protected $table = 'inbound_shipment_item';

    protected $fillable = [
        'inbound_shipment_id',
        'item_id',
    ];
}
