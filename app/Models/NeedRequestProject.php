<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NeedRequestProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'is_enabled',
        'allowed_id_count',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'allowed_id_count' => 'integer',
    ];

    // --- Relationships ---

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // --- Scopes ---

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    // --- Static Helpers ---

    /**
     * Get all enabled projects for need requests
     */
    public static function getEnabledProjects()
    {
        return Project::whereHas('needRequestProject', function ($query) {
            $query->where('is_enabled', true);
        })->orWhereDoesntHave('needRequestProject')->get();
    }

    /**
     * Toggle the enabled status for a project
     */
    public static function toggleFor($projectId): bool
    {
        $setting = self::firstOrCreate(
            ['project_id' => $projectId],
            ['is_enabled' => true]
        );
        
        $setting->update(['is_enabled' => !$setting->is_enabled]);
        
        return $setting->is_enabled;
    }
}
