<?php

namespace Database\Factories;

use App\Models\NeedRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class NeedRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NeedRequest::class;

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
