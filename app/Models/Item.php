<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\ItemFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
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
    protected $filter = ItemFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inbound_shipment_id',
        'name',
        'description',
        'weight',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'weight' => 'float',
        'quantity' => 'integer',
    ];

    /**
     * Inbound shipments relation (many-to-many).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    // العلاقات
    public function inboundShipment()
    {
        return $this->belongsTo(InboundShipment::class);
    }
    // الأصناف المستخدمة في الطرود
    public function packageContents()
    {
        return $this->hasMany(PackageContent::class);
    }
}