<?php
namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'weight',
    ];

    public function items()
    {
        return $this->hasMany(InternalPackageItem::class);
    }
}
