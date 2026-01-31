<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\NeedRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NeedRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_need_requests()
    {
        $this->actingAsAdmin();

        NeedRequest::factory()->count(2)->create();

        $this->getJson(route('api.need_requests.index'))
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
    public function test_need_requests_select2_api()
    {
        NeedRequest::factory()->count(5)->create();

        $response = $this->getJson(route('api.need_requests.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.need_requests.select', ['selected_id' => 4]))
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
    public function it_can_display_the_need_request_details()
    {
        $this->actingAsAdmin();

        $need_request = NeedRequest::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.need_requests.show', $need_request))
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
