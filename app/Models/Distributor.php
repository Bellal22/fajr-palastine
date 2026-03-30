<?php

namespace App\Models;

use Parental\HasParent;
use App\Http\Filters\DistributorFilter;
use App\Http\Resources\CustomerResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Distributor extends User
{
    use HasFactory;
    use HasParent;

    /**
     * The model filter name.
     *
     * @var string
     */
    protected $filter = DistributorFilter::class;

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
        return route('dashboard.distributors.show', $this);
    }
}