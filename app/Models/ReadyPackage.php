<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\ReadyPackageFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReadyPackage extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = ReadyPackageFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'weight',
        'quantity',
        'remaining_quantity',
        'min_stock',
    ];

    protected $casts = [
        'weight' => 'float',
        'quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'min_stock' => 'integer',
    ];

    /**
     * Scope for low stock packages.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('remaining_quantity', '<=', 'min_stock');
    }
    // العلاقات
    public function inboundShipment()
    {
        return $this->belongsTo(InboundShipment::class);
    }
    // محتويات الطرد (polymorphic)
    public function contents()
    {
        return $this->morphMany(PackageContent::class, 'package');
    }
    // الإرساليات الصادرة
    public function outboundShipmentItems()
    {
        return $this->morphMany(OutboundShipmentItem::class, 'shippable');
    }
}