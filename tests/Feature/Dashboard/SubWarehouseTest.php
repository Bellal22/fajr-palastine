<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\SubWarehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubWarehouseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_sub_warehouses()
    {
        $this->actingAsAdmin();

        SubWarehouse::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.sub_warehouses.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_sub_warehouse_details()
    {
        $this->actingAsAdmin();

        $sub_warehouse = SubWarehouse::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.sub_warehouses.show', $sub_warehouse))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_sub_warehouses_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.sub_warehouses.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_sub_warehouse()
    {
        $this->actingAsAdmin();

        $sub_warehousesCount = SubWarehouse::count();

        $response = $this->post(
            route('dashboard.sub_warehouses.store'),
            SubWarehouse::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $sub_warehouse = SubWarehouse::all()->last();

        $this->assertEquals(SubWarehouse::count(), $sub_warehousesCount + 1);

        $this->assertEquals($sub_warehouse->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_sub_warehouses_edit_form()
    {
        $this->actingAsAdmin();

        $sub_warehouse = SubWarehouse::factory()->create();

        $this->get(route('dashboard.sub_warehouses.edit', $sub_warehouse))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_sub_warehouse()
    {
        $this->actingAsAdmin();

        $sub_warehouse = SubWarehouse::factory()->create();

        $response = $this->put(
            route('dashboard.sub_warehouses.update', $sub_warehouse),
            SubWarehouse::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $sub_warehouse->refresh();

        $response->assertRedirect();

        $this->assertEquals($sub_warehouse->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_sub_warehouse()
    {
        $this->actingAsAdmin();

        $sub_warehouse = SubWarehouse::factory()->create();

        $sub_warehousesCount = SubWarehouse::count();

        $response = $this->delete(route('dashboard.sub_warehouses.destroy', $sub_warehouse));

        $response->assertRedirect();

        $this->assertEquals(SubWarehouse::count(), $sub_warehousesCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_sub_warehouses()
    {
        if (! $this->useSoftDeletes($model = SubWarehouse::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        SubWarehouse::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.sub_warehouses.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_sub_warehouse_details()
    {
        if (! $this->useSoftDeletes($model = SubWarehouse::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $sub_warehouse = SubWarehouse::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.sub_warehouses.trashed.show', $sub_warehouse));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_sub_warehouse()
    {
        if (! $this->useSoftDeletes($model = SubWarehouse::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $sub_warehouse = SubWarehouse::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.sub_warehouses.restore', $sub_warehouse));

        $response->assertRedirect();

        $this->assertNull($sub_warehouse->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_sub_warehouse()
    {
        if (! $this->useSoftDeletes($model = SubWarehouse::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $sub_warehouse = SubWarehouse::factory()->create(['deleted_at' => now()]);

        $sub_warehouseCount = SubWarehouse::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.sub_warehouses.forceDelete', $sub_warehouse));

        $response->assertRedirect();

        $this->assertEquals(SubWarehouse::withoutTrashed()->count(), $sub_warehouseCount - 1);
    }

    /** @test */
    public function it_can_filter_sub_warehouses_by_name()
    {
        $this->actingAsAdmin();

        SubWarehouse::factory()->create([
            'name' => 'Foo',
        ]);

        SubWarehouse::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.sub_warehouses.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('sub_warehouses.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
