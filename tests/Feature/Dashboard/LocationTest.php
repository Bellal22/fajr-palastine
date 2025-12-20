<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_locations()
    {
        $this->actingAsAdmin();

        Location::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.locations.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_location_details()
    {
        $this->actingAsAdmin();

        $location = Location::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.locations.show', $location))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_locations_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.locations.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_location()
    {
        $this->actingAsAdmin();

        $locationsCount = Location::count();

        $response = $this->post(
            route('dashboard.locations.store'),
            Location::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $location = Location::all()->last();

        $this->assertEquals(Location::count(), $locationsCount + 1);

        $this->assertEquals($location->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_locations_edit_form()
    {
        $this->actingAsAdmin();

        $location = Location::factory()->create();

        $this->get(route('dashboard.locations.edit', $location))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_location()
    {
        $this->actingAsAdmin();

        $location = Location::factory()->create();

        $response = $this->put(
            route('dashboard.locations.update', $location),
            Location::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $location->refresh();

        $response->assertRedirect();

        $this->assertEquals($location->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_location()
    {
        $this->actingAsAdmin();

        $location = Location::factory()->create();

        $locationsCount = Location::count();

        $response = $this->delete(route('dashboard.locations.destroy', $location));

        $response->assertRedirect();

        $this->assertEquals(Location::count(), $locationsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_locations()
    {
        if (! $this->useSoftDeletes($model = Location::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Location::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.locations.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_location_details()
    {
        if (! $this->useSoftDeletes($model = Location::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $location = Location::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.locations.trashed.show', $location));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_location()
    {
        if (! $this->useSoftDeletes($model = Location::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $location = Location::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.locations.restore', $location));

        $response->assertRedirect();

        $this->assertNull($location->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_location()
    {
        if (! $this->useSoftDeletes($model = Location::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $location = Location::factory()->create(['deleted_at' => now()]);

        $locationCount = Location::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.locations.forceDelete', $location));

        $response->assertRedirect();

        $this->assertEquals(Location::withoutTrashed()->count(), $locationCount - 1);
    }

    /** @test */
    public function it_can_filter_locations_by_name()
    {
        $this->actingAsAdmin();

        Location::factory()->create([
            'name' => 'Foo',
        ]);

        Location::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.locations.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('locations.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
