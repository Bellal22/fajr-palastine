<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Block;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_blocks()
    {
        $this->actingAsAdmin();

        Block::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.blocks.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_block_details()
    {
        $this->actingAsAdmin();

        $block = Block::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.blocks.show', $block))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_blocks_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.blocks.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_block()
    {
        $this->actingAsAdmin();

        $blocksCount = Block::count();

        $response = $this->post(
            route('dashboard.blocks.store'),
            Block::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $block = Block::all()->last();

        $this->assertEquals(Block::count(), $blocksCount + 1);

        $this->assertEquals($block->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_blocks_edit_form()
    {
        $this->actingAsAdmin();

        $block = Block::factory()->create();

        $this->get(route('dashboard.blocks.edit', $block))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_block()
    {
        $this->actingAsAdmin();

        $block = Block::factory()->create();

        $response = $this->put(
            route('dashboard.blocks.update', $block),
            Block::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $block->refresh();

        $response->assertRedirect();

        $this->assertEquals($block->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_block()
    {
        $this->actingAsAdmin();

        $block = Block::factory()->create();

        $blocksCount = Block::count();

        $response = $this->delete(route('dashboard.blocks.destroy', $block));

        $response->assertRedirect();

        $this->assertEquals(Block::count(), $blocksCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_blocks()
    {
        if (! $this->useSoftDeletes($model = Block::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Block::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.blocks.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_block_details()
    {
        if (! $this->useSoftDeletes($model = Block::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $block = Block::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.blocks.trashed.show', $block));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_block()
    {
        if (! $this->useSoftDeletes($model = Block::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $block = Block::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.blocks.restore', $block));

        $response->assertRedirect();

        $this->assertNull($block->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_block()
    {
        if (! $this->useSoftDeletes($model = Block::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $block = Block::factory()->create(['deleted_at' => now()]);

        $blockCount = Block::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.blocks.forceDelete', $block));

        $response->assertRedirect();

        $this->assertEquals(Block::withoutTrashed()->count(), $blockCount - 1);
    }

    /** @test */
    public function it_can_filter_blocks_by_name()
    {
        $this->actingAsAdmin();

        Block::factory()->create([
            'name' => 'Foo',
        ]);

        Block::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.blocks.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('blocks.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
