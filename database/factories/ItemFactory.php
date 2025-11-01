<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->optional()->sentence(10),
            'package' => $this->faker->boolean(30),
            'type' => $this->faker->numberBetween(0, 5),
            'weight' => $this->faker->optional()->randomFloat(2, 0, 100),
            'quantity' => $this->faker->optional()->numberBetween(0, 1000),
        ];
    }
}
