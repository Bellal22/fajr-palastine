<?php

namespace Database\Factories;

use App\Models\InboundShipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class InboundShipmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InboundShipment::class;

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
