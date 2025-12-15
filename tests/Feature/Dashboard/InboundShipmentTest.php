<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\InboundShipment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InboundShipmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_inbound_shipments()
    {
        $this->actingAsAdmin();

        InboundShipment::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.inbound_shipments.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_inbound_shipment_details()
    {
        $this->actingAsAdmin();

        $inbound_shipment = InboundShipment::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.inbound_shipments.show', $inbound_shipment))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_inbound_shipments_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.inbound_shipments.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_inbound_shipment()
    {
        $this->actingAsAdmin();

        $inbound_shipmentsCount = InboundShipment::count();

        $response = $this->post(
            route('dashboard.inbound_shipments.store'),
            InboundShipment::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $inbound_shipment = InboundShipment::all()->last();

        $this->assertEquals(InboundShipment::count(), $inbound_shipmentsCount + 1);

        $this->assertEquals($inbound_shipment->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_inbound_shipments_edit_form()
    {
        $this->actingAsAdmin();

        $inbound_shipment = InboundShipment::factory()->create();

        $this->get(route('dashboard.inbound_shipments.edit', $inbound_shipment))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_inbound_shipment()
    {
        $this->actingAsAdmin();

        $inbound_shipment = InboundShipment::factory()->create();

        $response = $this->put(
            route('dashboard.inbound_shipments.update', $inbound_shipment),
            InboundShipment::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $inbound_shipment->refresh();

        $response->assertRedirect();

        $this->assertEquals($inbound_shipment->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_inbound_shipment()
    {
        $this->actingAsAdmin();

        $inbound_shipment = InboundShipment::factory()->create();

        $inbound_shipmentsCount = InboundShipment::count();

        $response = $this->delete(route('dashboard.inbound_shipments.destroy', $inbound_shipment));

        $response->assertRedirect();

        $this->assertEquals(InboundShipment::count(), $inbound_shipmentsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_inbound_shipments()
    {
        if (! $this->useSoftDeletes($model = InboundShipment::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        InboundShipment::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.inbound_shipments.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_inbound_shipment_details()
    {
        if (! $this->useSoftDeletes($model = InboundShipment::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $inbound_shipment = InboundShipment::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.inbound_shipments.trashed.show', $inbound_shipment));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_inbound_shipment()
    {
        if (! $this->useSoftDeletes($model = InboundShipment::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $inbound_shipment = InboundShipment::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.inbound_shipments.restore', $inbound_shipment));

        $response->assertRedirect();

        $this->assertNull($inbound_shipment->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_inbound_shipment()
    {
        if (! $this->useSoftDeletes($model = InboundShipment::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $inbound_shipment = InboundShipment::factory()->create(['deleted_at' => now()]);

        $inbound_shipmentCount = InboundShipment::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.inbound_shipments.forceDelete', $inbound_shipment));

        $response->assertRedirect();

        $this->assertEquals(InboundShipment::withoutTrashed()->count(), $inbound_shipmentCount - 1);
    }

    /** @test */
    public function it_can_filter_inbound_shipments_by_name()
    {
        $this->actingAsAdmin();

        InboundShipment::factory()->create([
            'name' => 'Foo',
        ]);

        InboundShipment::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.inbound_shipments.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('inbound_shipments.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
