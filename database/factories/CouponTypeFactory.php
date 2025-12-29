<?php

namespace Database\Factories;

use App\Models\CouponType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CouponType::class;

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
