<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, Filterable, Selectable;

    protected $table = 'employees';
    protected $guarded = [];
    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Relationship with projects
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_beneficiaries', 'employee_id', 'project_id')
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id')
            ->withTimestamps();
    }

    public function getFullName(): string
    {
        $nameParts = [
            $this->first_name ?? '',
            $this->father_name ?? '',
            $this->grandfather_name ?? '',
            $this->family_name ?? ''
        ];
        return trim(implode(' ', array_filter($nameParts))) ?: 'موظف غير مسمى';
    }

    public function getNameAttribute(): string
    {
        return $this->getFullName();
    }
}
