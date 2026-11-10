<?php

namespace App\Models;

use Parental\HasParent;
use App\Http\Filters\SupervisorFilter;
use App\Http\Resources\CustomerResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KitchenMonitor extends User
{
    use HasFactory;
    use HasParent;

    /**
     * The model filter name.
     *
     * @var string
     */
    protected $filter = SupervisorFilter::class;

    /**
     * Boot function to assign specific permissions automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $model->syncPermissions([
                'manage.projects',
                'manage.kitchen_cards',
                'manage.kitchen_complaints',
            ]);
        });
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return User::class;
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return 'user_id';
    }

    /**
     * @return \App\Http\Resources\CustomerResource
     */
    public function getResource()
    {
        return new CustomerResource($this);
    }

    /**
     * Get the dashboard profile link.
     *
     * @return string
     */
    public function dashboardProfile(): string
    {
        return route('dashboard.kitchen_monitors.show', $this);
    }
}
