<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\SubWarehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubWarehouseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_sub_warehouses()
    {
        $this->actingAsAdmin();

        SubWarehouse::factory()->count(2)->create();

        $this->getJson(route('api.sub_warehouses.index'))
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
    public function test_sub_warehouses_select2_api()
    {
        SubWarehouse::factory()->count(5)->create();

        $response = $this->getJson(route('api.sub_warehouses.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.sub_warehouses.select', ['selected_id' => 4]))
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
    public function it_can_display_the_sub_warehouse_details()
    {
        $this->actingAsAdmin();

        $sub_warehouse = SubWarehouse::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.sub_warehouses.show', $sub_warehouse))
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
