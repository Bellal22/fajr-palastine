<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\Traits\Selectable;
use App\Http\Filters\Filterable;

class KitchenComplaint extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

    protected $fillable = [
        'camp_name',
        'card_number',
        'complainant_name',
        'mobile_number',
        'complaint_title',
        'complaint_text',
        'status',
        'response',
        'responded_at',
        'responded_by',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
