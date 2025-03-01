<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Neighborhood;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NeighborhoodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_neighborhoods()
    {
        $this->actingAsAdmin();

        Neighborhood::factory()->count(2)->create();

        $this->getJson(route('api.neighborhoods.index'))
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
    public function test_neighborhoods_select2_api()
    {
        Neighborhood::factory()->count(5)->create();

        $response = $this->getJson(route('api.neighborhoods.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.neighborhoods.select', ['selected_id' => 4]))
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
    public function it_can_display_the_neighborhood_details()
    {
        $this->actingAsAdmin();

        $neighborhood = Neighborhood::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.neighborhoods.show', $neighborhood))
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
