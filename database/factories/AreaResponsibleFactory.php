<?php

namespace Database\Factories;

use App\Models\AreaResponsible;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaResponsibleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AreaResponsible::class;

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
