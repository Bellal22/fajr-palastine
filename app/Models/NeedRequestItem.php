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

    /**
     * Check if a person has been requested in the last 30 days.
     *
     * @param int $personId
     * @param int|null $excludeRequestId
     * @return bool
     */
    public static function isRequestedInLast30Days($personId, $excludeRequestId = null): bool
    {
        return self::where('person_id', $personId)
            ->when($excludeRequestId, function ($query) use ($excludeRequestId) {
                $query->where('need_request_id', '!=', $excludeRequestId);
            })
            ->whereHas('needRequest', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->exists();
    }
}
