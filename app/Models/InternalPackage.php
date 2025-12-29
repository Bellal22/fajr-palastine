<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\InternalPackageFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternalPackage extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = InternalPackageFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'weight',
        'quantity',
        'created_by',
    ];
    protected $casts = [
        'weight' => 'float',
        'quantity' => 'integer',
    ];
    // العلاقات
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
    // الإرساليات الواردة
    public function inboundShipmentItems()
    {
        return $this->morphMany(InboundShipmentItem::class, 'shippable');
    }
    // المخازن التي تمت إرسالها إليها
    public function warehouses()
    {
        return $this->belongsToMany(SubWarehouse::class)->withPivot('quantity', 'price')->as('warehouse_item');
    }
}