<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Block;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_blocks()
    {
        $this->actingAsAdmin();

        Block::factory()->count(2)->create();

        $this->getJson(route('api.blocks.index'))
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
    public function test_blocks_select2_api()
    {
        Block::factory()->count(5)->create();

        $response = $this->getJson(route('api.blocks.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.blocks.select', ['selected_id' => 4]))
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
    public function it_can_display_the_block_details()
    {
        $this->actingAsAdmin();

        $block = Block::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.blocks.show', $block))
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
