<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NeedRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'need_request_id',
        'person_id',
        'status',
        'notes',
    ];

    // --- Relationships ---

    public function needRequest()
    {
        return $this->belongsTo(NeedRequest::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    // --- Scopes ---

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
