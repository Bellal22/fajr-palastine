<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\SubCity;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubCityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_sub_cities()
    {
        $this->actingAsAdmin();

        SubCity::factory()->count(2)->create();

        $this->getJson(route('api.sub_cities.index'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    /** @test */
    public function test_sub_cities_select2_api()
    {
        SubCity::factory()->count(5)->create();

        $response = $this->getJson(route('api.sub_cities.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.sub_cities.select', ['selected_id' => 4]))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 4);

        $this->assertCount(5, $response->json('data'));
    }

    /** @test */
    public function it_can_display_the_sub_city_details()
    {
        $this->actingAsAdmin();

        $sub_city = SubCity::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.sub_cities.show', $sub_city))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ],
            ]);

        $this->assertEquals($response->json('data.name'), 'Foo');
    }
}
