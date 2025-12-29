<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\OutboundShipment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OutboundShipmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_outbound_shipments()
    {
        $this->actingAsAdmin();

        OutboundShipment::factory()->count(2)->create();

        $this->getJson(route('api.outbound_shipments.index'))
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
    public function test_outbound_shipments_select2_api()
    {
        OutboundShipment::factory()->count(5)->create();

        $response = $this->getJson(route('api.outbound_shipments.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.outbound_shipments.select', ['selected_id' => 4]))
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
    public function it_can_display_the_outbound_shipment_details()
    {
        $this->actingAsAdmin();

        $outbound_shipment = OutboundShipment::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.outbound_shipments.show', $outbound_shipment))
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
