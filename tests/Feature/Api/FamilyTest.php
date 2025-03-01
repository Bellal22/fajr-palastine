<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FamilyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_families()
    {
        $this->actingAsAdmin();

        Family::factory()->count(2)->create();

        $this->getJson(route('api.families.index'))
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
    public function test_families_select2_api()
    {
        Family::factory()->count(5)->create();

        $response = $this->getJson(route('api.families.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.families.select', ['selected_id' => 4]))
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
    public function it_can_display_the_family_details()
    {
        $this->actingAsAdmin();

        $family = Family::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.families.show', $family))
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
