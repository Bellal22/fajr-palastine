<?php

namespace Database\Factories;

use App\Models\SubWarehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubWarehouseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubWarehouse::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
