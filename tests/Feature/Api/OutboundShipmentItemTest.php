<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\OutboundShipmentItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OutboundShipmentItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_outbound_shipment_items()
    {
        $this->actingAsAdmin();

        OutboundShipmentItem::factory()->count(2)->create();

        $this->getJson(route('api.outbound_shipment_items.index'))
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
    public function test_outbound_shipment_items_select2_api()
    {
        OutboundShipmentItem::factory()->count(5)->create();

        $response = $this->getJson(route('api.outbound_shipment_items.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.outbound_shipment_items.select', ['selected_id' => 4]))
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
    public function it_can_display_the_outbound_shipment_item_details()
    {
        $this->actingAsAdmin();

        $outbound_shipment_item = OutboundShipmentItem::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.outbound_shipment_items.show', $outbound_shipment_item))
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
