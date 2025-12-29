<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\OutboundShipment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OutboundShipmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_outbound_shipments()
    {
        $this->actingAsAdmin();

        OutboundShipment::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.outbound_shipments.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_outbound_shipment_details()
    {
        $this->actingAsAdmin();

        $outbound_shipment = OutboundShipment::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.outbound_shipments.show', $outbound_shipment))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_outbound_shipments_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.outbound_shipments.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_outbound_shipment()
    {
        $this->actingAsAdmin();

        $outbound_shipmentsCount = OutboundShipment::count();

        $response = $this->post(
            route('dashboard.outbound_shipments.store'),
            OutboundShipment::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $outbound_shipment = OutboundShipment::all()->last();

        $this->assertEquals(OutboundShipment::count(), $outbound_shipmentsCount + 1);

        $this->assertEquals($outbound_shipment->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_outbound_shipments_edit_form()
    {
        $this->actingAsAdmin();

        $outbound_shipment = OutboundShipment::factory()->create();

        $this->get(route('dashboard.outbound_shipments.edit', $outbound_shipment))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_outbound_shipment()
    {
        $this->actingAsAdmin();

        $outbound_shipment = OutboundShipment::factory()->create();

        $response = $this->put(
            route('dashboard.outbound_shipments.update', $outbound_shipment),
            OutboundShipment::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $outbound_shipment->refresh();

        $response->assertRedirect();

        $this->assertEquals($outbound_shipment->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_outbound_shipment()
    {
        $this->actingAsAdmin();

        $outbound_shipment = OutboundShipment::factory()->create();

        $outbound_shipmentsCount = OutboundShipment::count();

        $response = $this->delete(route('dashboard.outbound_shipments.destroy', $outbound_shipment));

        $response->assertRedirect();

        $this->assertEquals(OutboundShipment::count(), $outbound_shipmentsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_outbound_shipments()
    {
        if (! $this->useSoftDeletes($model = OutboundShipment::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        OutboundShipment::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.outbound_shipments.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_outbound_shipment_details()
    {
        if (! $this->useSoftDeletes($model = OutboundShipment::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $outbound_shipment = OutboundShipment::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.outbound_shipments.trashed.show', $outbound_shipment));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_outbound_shipment()
    {
        if (! $this->useSoftDeletes($model = OutboundShipment::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $outbound_shipment = OutboundShipment::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.outbound_shipments.restore', $outbound_shipment));

        $response->assertRedirect();

        $this->assertNull($outbound_shipment->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_outbound_shipment()
    {
        if (! $this->useSoftDeletes($model = OutboundShipment::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $outbound_shipment = OutboundShipment::factory()->create(['deleted_at' => now()]);

        $outbound_shipmentCount = OutboundShipment::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.outbound_shipments.forceDelete', $outbound_shipment));

        $response->assertRedirect();

        $this->assertEquals(OutboundShipment::withoutTrashed()->count(), $outbound_shipmentCount - 1);
    }

    /** @test */
    public function it_can_filter_outbound_shipments_by_name()
    {
        $this->actingAsAdmin();

        OutboundShipment::factory()->create([
            'name' => 'Foo',
        ]);

        OutboundShipment::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.outbound_shipments.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('outbound_shipments.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
