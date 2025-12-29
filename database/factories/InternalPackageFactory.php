<?php

namespace Database\Factories;

use App\Models\InternalPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class InternalPackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InternalPackage::class;

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
