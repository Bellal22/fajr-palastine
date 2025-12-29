<?php

namespace Database\Factories;

use App\Models\PackageContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageContentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PackageContent::class;

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
