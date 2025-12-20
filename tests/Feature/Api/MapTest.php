<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Map;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MapTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_maps()
    {
        $this->actingAsAdmin();

        Map::factory()->count(2)->create();

        $this->getJson(route('api.maps.index'))
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
    public function test_maps_select2_api()
    {
        Map::factory()->count(5)->create();

        $response = $this->getJson(route('api.maps.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.maps.select', ['selected_id' => 4]))
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
    public function it_can_display_the_map_details()
    {
        $this->actingAsAdmin();

        $map = Map::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.maps.show', $map))
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
