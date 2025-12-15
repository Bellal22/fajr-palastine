<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_items()
    {
        $this->actingAsAdmin();

        Item::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.items.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_item_details()
    {
        $this->actingAsAdmin();

        $item = Item::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.items.show', $item))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_items_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.items.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_item()
    {
        $this->actingAsAdmin();

        $itemsCount = Item::count();

        $response = $this->post(
            route('dashboard.items.store'),
            Item::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $item = Item::all()->last();

        $this->assertEquals(Item::count(), $itemsCount + 1);

        $this->assertEquals($item->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_items_edit_form()
    {
        $this->actingAsAdmin();

        $item = Item::factory()->create();

        $this->get(route('dashboard.items.edit', $item))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_item()
    {
        $this->actingAsAdmin();

        $item = Item::factory()->create();

        $response = $this->put(
            route('dashboard.items.update', $item),
            Item::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $item->refresh();

        $response->assertRedirect();

        $this->assertEquals($item->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_item()
    {
        $this->actingAsAdmin();

        $item = Item::factory()->create();

        $itemsCount = Item::count();

        $response = $this->delete(route('dashboard.items.destroy', $item));

        $response->assertRedirect();

        $this->assertEquals(Item::count(), $itemsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_items()
    {
        if (! $this->useSoftDeletes($model = Item::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Item::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.items.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_item_details()
    {
        if (! $this->useSoftDeletes($model = Item::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $item = Item::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.items.trashed.show', $item));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_item()
    {
        if (! $this->useSoftDeletes($model = Item::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $item = Item::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.items.restore', $item));

        $response->assertRedirect();

        $this->assertNull($item->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_item()
    {
        if (! $this->useSoftDeletes($model = Item::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $item = Item::factory()->create(['deleted_at' => now()]);

        $itemCount = Item::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.items.forceDelete', $item));

        $response->assertRedirect();

        $this->assertEquals(Item::withoutTrashed()->count(), $itemCount - 1);
    }

    /** @test */
    public function it_can_filter_items_by_name()
    {
        $this->actingAsAdmin();

        Item::factory()->create([
            'name' => 'Foo',
        ]);

        Item::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.items.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('items.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
