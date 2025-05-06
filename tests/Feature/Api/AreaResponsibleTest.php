<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\AreaResponsible;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AreaResponsibleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_area_responsibles()
    {
        $this->actingAsAdmin();

        AreaResponsible::factory()->count(2)->create();

        $this->getJson(route('api.area_responsibles.index'))
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
    public function test_area_responsibles_select2_api()
    {
        AreaResponsible::factory()->count(5)->create();

        $response = $this->getJson(route('api.area_responsibles.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.area_responsibles.select', ['selected_id' => 4]))
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
    public function it_can_display_the_area_responsible_details()
    {
        $this->actingAsAdmin();

        $area_responsible = AreaResponsible::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.area_responsibles.show', $area_responsible))
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
