<?php

namespace Database\Factories;

use App\Models\OutboundShipmentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutboundShipmentItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OutboundShipmentItem::class;

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
