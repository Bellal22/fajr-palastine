<?php

namespace Database\Factories;

use App\Models\SubCity;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubCityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubCity::class;

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
