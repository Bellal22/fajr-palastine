<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Map;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MapTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_maps()
    {
        $this->actingAsAdmin();

        Map::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.maps.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_map_details()
    {
        $this->actingAsAdmin();

        $map = Map::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.maps.show', $map))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_maps_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.maps.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_map()
    {
        $this->actingAsAdmin();

        $mapsCount = Map::count();

        $response = $this->post(
            route('dashboard.maps.store'),
            Map::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $map = Map::all()->last();

        $this->assertEquals(Map::count(), $mapsCount + 1);

        $this->assertEquals($map->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_maps_edit_form()
    {
        $this->actingAsAdmin();

        $map = Map::factory()->create();

        $this->get(route('dashboard.maps.edit', $map))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_map()
    {
        $this->actingAsAdmin();

        $map = Map::factory()->create();

        $response = $this->put(
            route('dashboard.maps.update', $map),
            Map::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $map->refresh();

        $response->assertRedirect();

        $this->assertEquals($map->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_map()
    {
        $this->actingAsAdmin();

        $map = Map::factory()->create();

        $mapsCount = Map::count();

        $response = $this->delete(route('dashboard.maps.destroy', $map));

        $response->assertRedirect();

        $this->assertEquals(Map::count(), $mapsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_maps()
    {
        if (! $this->useSoftDeletes($model = Map::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Map::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.maps.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_map_details()
    {
        if (! $this->useSoftDeletes($model = Map::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $map = Map::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.maps.trashed.show', $map));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_map()
    {
        if (! $this->useSoftDeletes($model = Map::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $map = Map::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.maps.restore', $map));

        $response->assertRedirect();

        $this->assertNull($map->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_map()
    {
        if (! $this->useSoftDeletes($model = Map::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $map = Map::factory()->create(['deleted_at' => now()]);

        $mapCount = Map::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.maps.forceDelete', $map));

        $response->assertRedirect();

        $this->assertEquals(Map::withoutTrashed()->count(), $mapCount - 1);
    }

    /** @test */
    public function it_can_filter_maps_by_name()
    {
        $this->actingAsAdmin();

        Map::factory()->create([
            'name' => 'Foo',
        ]);

        Map::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.maps.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('maps.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
