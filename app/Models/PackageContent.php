<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\PackageContentFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageContent extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = PackageContentFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id',
        'package_type',
        'item_id',
        'quantity',
    ];
    protected $casts = [
        'quantity' => 'integer',
    ];
    // Polymorphic relation
    public function package()
    {
        return $this->morphTo();
    }
    // الصنف
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}