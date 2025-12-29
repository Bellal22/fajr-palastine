<?php

namespace Database\Factories;

use App\Models\ReadyPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadyPackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReadyPackage::class;

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
