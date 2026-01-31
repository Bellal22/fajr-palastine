<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NeedRequestSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervisor_id',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    // --- Relationships ---

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // --- Static Helpers ---

    /**
     * Check if need requests are enabled for a supervisor
     */
    public static function isEnabledFor($supervisorId): bool
    {
        $setting = self::where('supervisor_id', $supervisorId)->first();
        return $setting ? $setting->is_enabled : false;
    }

    /**
     * Toggle the enabled status for a supervisor
     */
    public static function toggleFor($supervisorId): bool
    {
        $setting = self::firstOrCreate(
            ['supervisor_id' => $supervisorId],
            ['is_enabled' => false]
        );
        
        $setting->update(['is_enabled' => !$setting->is_enabled]);
        
        return $setting->is_enabled;
    }
}
