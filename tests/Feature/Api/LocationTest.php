<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_locations()
    {
        $this->actingAsAdmin();

        Location::factory()->count(2)->create();

        $this->getJson(route('api.locations.index'))
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
    public function test_locations_select2_api()
    {
        Location::factory()->count(5)->create();

        $response = $this->getJson(route('api.locations.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.locations.select', ['selected_id' => 4]))
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
    public function it_can_display_the_location_details()
    {
        $this->actingAsAdmin();

        $location = Location::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.locations.show', $location))
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
