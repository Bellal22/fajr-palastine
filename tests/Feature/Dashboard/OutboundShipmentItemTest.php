<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\OutboundShipmentItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OutboundShipmentItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_outbound_shipment_items()
    {
        $this->actingAsAdmin();

        OutboundShipmentItem::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.outbound_shipment_items.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_outbound_shipment_item_details()
    {
        $this->actingAsAdmin();

        $outbound_shipment_item = OutboundShipmentItem::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.outbound_shipment_items.show', $outbound_shipment_item))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_outbound_shipment_items_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.outbound_shipment_items.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_outbound_shipment_item()
    {
        $this->actingAsAdmin();

        $outbound_shipment_itemsCount = OutboundShipmentItem::count();

        $response = $this->post(
            route('dashboard.outbound_shipment_items.store'),
            OutboundShipmentItem::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $outbound_shipment_item = OutboundShipmentItem::all()->last();

        $this->assertEquals(OutboundShipmentItem::count(), $outbound_shipment_itemsCount + 1);

        $this->assertEquals($outbound_shipment_item->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_outbound_shipment_items_edit_form()
    {
        $this->actingAsAdmin();

        $outbound_shipment_item = OutboundShipmentItem::factory()->create();

        $this->get(route('dashboard.outbound_shipment_items.edit', $outbound_shipment_item))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_outbound_shipment_item()
    {
        $this->actingAsAdmin();

        $outbound_shipment_item = OutboundShipmentItem::factory()->create();

        $response = $this->put(
            route('dashboard.outbound_shipment_items.update', $outbound_shipment_item),
            OutboundShipmentItem::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $outbound_shipment_item->refresh();

        $response->assertRedirect();

        $this->assertEquals($outbound_shipment_item->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_outbound_shipment_item()
    {
        $this->actingAsAdmin();

        $outbound_shipment_item = OutboundShipmentItem::factory()->create();

        $outbound_shipment_itemsCount = OutboundShipmentItem::count();

        $response = $this->delete(route('dashboard.outbound_shipment_items.destroy', $outbound_shipment_item));

        $response->assertRedirect();

        $this->assertEquals(OutboundShipmentItem::count(), $outbound_shipment_itemsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_outbound_shipment_items()
    {
        if (! $this->useSoftDeletes($model = OutboundShipmentItem::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        OutboundShipmentItem::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.outbound_shipment_items.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_outbound_shipment_item_details()
    {
        if (! $this->useSoftDeletes($model = OutboundShipmentItem::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $outbound_shipment_item = OutboundShipmentItem::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.outbound_shipment_items.trashed.show', $outbound_shipment_item));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_outbound_shipment_item()
    {
        if (! $this->useSoftDeletes($model = OutboundShipmentItem::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $outbound_shipment_item = OutboundShipmentItem::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.outbound_shipment_items.restore', $outbound_shipment_item));

        $response->assertRedirect();

        $this->assertNull($outbound_shipment_item->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_outbound_shipment_item()
    {
        if (! $this->useSoftDeletes($model = OutboundShipmentItem::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $outbound_shipment_item = OutboundShipmentItem::factory()->create(['deleted_at' => now()]);

        $outbound_shipment_itemCount = OutboundShipmentItem::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.outbound_shipment_items.forceDelete', $outbound_shipment_item));

        $response->assertRedirect();

        $this->assertEquals(OutboundShipmentItem::withoutTrashed()->count(), $outbound_shipment_itemCount - 1);
    }

    /** @test */
    public function it_can_filter_outbound_shipment_items_by_name()
    {
        $this->actingAsAdmin();

        OutboundShipmentItem::factory()->create([
            'name' => 'Foo',
        ]);

        OutboundShipmentItem::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.outbound_shipment_items.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('outbound_shipment_items.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
