<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_regions()
    {
        $this->actingAsAdmin();

        Region::factory()->count(2)->create();

        $this->getJson(route('api.regions.index'))
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
    public function test_regions_select2_api()
    {
        Region::factory()->count(5)->create();

        $response = $this->getJson(route('api.regions.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.regions.select', ['selected_id' => 4]))
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
    public function it_can_display_the_region_details()
    {
        $this->actingAsAdmin();

        $region = Region::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.regions.show', $region))
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
