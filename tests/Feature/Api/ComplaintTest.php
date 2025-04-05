<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Complaint;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComplaintTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_complaints()
    {
        $this->actingAsAdmin();

        Complaint::factory()->count(2)->create();

        $this->getJson(route('api.complaints.index'))
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
    public function test_complaints_select2_api()
    {
        Complaint::factory()->count(5)->create();

        $response = $this->getJson(route('api.complaints.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.complaints.select', ['selected_id' => 4]))
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
    public function it_can_display_the_complaint_details()
    {
        $this->actingAsAdmin();

        $complaint = Complaint::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.complaints.show', $complaint))
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
