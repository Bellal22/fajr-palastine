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
        'shipment_number',
        'inbound_type',
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->shipment_number)) {
                $model->shipment_number = 'IS-' . date('YmdHis') . '-' . rand(1000, 9999);
            }
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    // العلاقات
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    // إذا كان النوع "صنف مفرد"
    public function items()
    {
        return $this->hasMany(Item::class);
    }
    // إذا كان النوع "طرد جاهز"
    public function readyPackages()
    {
        return $this->hasMany(ReadyPackage::class);
    }
    // Accessor للتحقق من النوع
    public function isReadyPackage()
    {
        return $this->inbound_type === 'ready_package';
    }
    public function isSingleItem()
    {
        return $this->inbound_type === 'single_item';
    }
    // Scope لتصفية المدخلات حسب النوع
    public function scopeByType($query, $type)
    {
        if ($type == 'single_item') {
            return $query->where('inbound_type', '=', 'single_item');
        } elseif ($type == 'ready_package') {
            return $query->where('inbound_type', '=', 'ready_package');
        }
    }
}