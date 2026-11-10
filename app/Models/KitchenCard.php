<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\Traits\Selectable;
use App\Http\Filters\Filterable;

class KitchenCard extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    protected $fillable = [
        'camp_name',
        'card_number',
        'barcode',
    ];
}
