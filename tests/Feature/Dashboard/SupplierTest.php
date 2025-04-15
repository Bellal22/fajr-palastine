<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_suppliers()
    {
        $this->actingAsAdmin();

        Supplier::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.suppliers.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_supplier_details()
    {
        $this->actingAsAdmin();

        $supplier = Supplier::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.suppliers.show', $supplier))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_suppliers_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.suppliers.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_supplier()
    {
        $this->actingAsAdmin();

        $suppliersCount = Supplier::count();

        $response = $this->post(
            route('dashboard.suppliers.store'),
            Supplier::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $supplier = Supplier::all()->last();

        $this->assertEquals(Supplier::count(), $suppliersCount + 1);

        $this->assertEquals($supplier->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_suppliers_edit_form()
    {
        $this->actingAsAdmin();

        $supplier = Supplier::factory()->create();

        $this->get(route('dashboard.suppliers.edit', $supplier))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_supplier()
    {
        $this->actingAsAdmin();

        $supplier = Supplier::factory()->create();

        $response = $this->put(
            route('dashboard.suppliers.update', $supplier),
            Supplier::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $supplier->refresh();

        $response->assertRedirect();

        $this->assertEquals($supplier->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_supplier()
    {
        $this->actingAsAdmin();

        $supplier = Supplier::factory()->create();

        $suppliersCount = Supplier::count();

        $response = $this->delete(route('dashboard.suppliers.destroy', $supplier));

        $response->assertRedirect();

        $this->assertEquals(Supplier::count(), $suppliersCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_suppliers()
    {
        if (! $this->useSoftDeletes($model = Supplier::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Supplier::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.suppliers.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_supplier_details()
    {
        if (! $this->useSoftDeletes($model = Supplier::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $supplier = Supplier::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.suppliers.trashed.show', $supplier));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_supplier()
    {
        if (! $this->useSoftDeletes($model = Supplier::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $supplier = Supplier::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.suppliers.restore', $supplier));

        $response->assertRedirect();

        $this->assertNull($supplier->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_supplier()
    {
        if (! $this->useSoftDeletes($model = Supplier::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $supplier = Supplier::factory()->create(['deleted_at' => now()]);

        $supplierCount = Supplier::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.suppliers.forceDelete', $supplier));

        $response->assertRedirect();

        $this->assertEquals(Supplier::withoutTrashed()->count(), $supplierCount - 1);
    }

    /** @test */
    public function it_can_filter_suppliers_by_name()
    {
        $this->actingAsAdmin();

        Supplier::factory()->create([
            'name' => 'Foo',
        ]);

        Supplier::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.suppliers.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('suppliers.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
