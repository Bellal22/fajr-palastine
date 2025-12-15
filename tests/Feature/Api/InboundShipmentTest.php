<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\InboundShipment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InboundShipmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_inbound_shipments()
    {
        $this->actingAsAdmin();

        InboundShipment::factory()->count(2)->create();

        $this->getJson(route('api.inbound_shipments.index'))
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
    public function test_inbound_shipments_select2_api()
    {
        InboundShipment::factory()->count(5)->create();

        $response = $this->getJson(route('api.inbound_shipments.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.inbound_shipments.select', ['selected_id' => 4]))
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
    public function it_can_display_the_inbound_shipment_details()
    {
        $this->actingAsAdmin();

        $inbound_shipment = InboundShipment::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.inbound_shipments.show', $inbound_shipment))
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
