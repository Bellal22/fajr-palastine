<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplaintType extends Model
{
    use HasFactory, Filterable, Selectable, SoftDeletes;

    protected $fillable = [
        'name',
        'is_active',
        'inactive_response',
        'cooldown_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cooldown_days' => 'integer',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'complaint_type_id');
    }
}
