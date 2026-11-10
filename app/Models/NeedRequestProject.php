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
        'deadline',
        'min_family_members',
        'max_family_members',
        'gender',
        'social_status',
        'employment_status',
        'housing_type',
        'has_condition',
        'min_age',
        'max_age',
        'child_min_age',
        'child_max_age',
        'child_count',
        'has_disability',
        'has_chronic_disease',
        'target_neighborhoods',
        'criteria_notes',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'allowed_id_count' => 'integer',
        'deadline' => 'datetime',
        'social_status' => 'array',
        'employment_status' => 'array',
        'housing_type' => 'array',
        'target_neighborhoods' => 'array',
        'has_condition' => 'boolean',
        'has_disability' => 'boolean',
        'has_chronic_disease' => 'boolean',
    ];

    // --- Relationships ---

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'need_request_project_supervisor', 'need_request_project_id', 'user_id');
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
        return Project::active()->whereHas('needRequestProject', function ($query) {
            $query->where('is_enabled', true)
                ->where(function ($q) {
                    $q->whereNull('deadline')
                        ->orWhere('deadline', '>', now());
                });
        })->get();
    }

    /**
     * Toggle the enabled status for a project
     */
    public static function toggleFor($projectId): bool
    {
        // Get the latest activation
        $latest = self::where('project_id', $projectId)->latest()->first();

        // If currently enabled, disable it (toggle logic)
        if ($latest && $latest->is_enabled) {
            $latest->update(['is_enabled' => false]);
            return false;
        }

        // If none exists or latest is disabled, create a NEW activation
        $data = $latest ? $latest->toArray() : ['project_id' => $projectId];
        unset($data['id'], $data['created_at'], $data['updated_at']);
        $data['is_enabled'] = true;
        // Reset deadline if it was previously expired to allow "extension"
        if (isset($data['deadline']) && Carbon::parse($data['deadline'])->isPast()) {
            $data['deadline'] = now()->addDays(7); // Default extension
        }

        self::create($data);
        return true;
    }

    /**
     * Check and deactivate all projects that have passed their deadline
     */
    public static function checkAndExpire(): int
    {
        // We no longer automatically set is_enabled to false.
        // The expiration is handled by query logic (deadline > now())
        // This keeps the request visible for admins/supervisors as "Expired".
        return 0;
    }
}
