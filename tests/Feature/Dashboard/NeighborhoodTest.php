<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Neighborhood;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NeighborhoodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_neighborhoods()
    {
        $this->actingAsAdmin();

        Neighborhood::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.neighborhoods.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_neighborhood_details()
    {
        $this->actingAsAdmin();

        $neighborhood = Neighborhood::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.neighborhoods.show', $neighborhood))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_neighborhoods_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.neighborhoods.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_neighborhood()
    {
        $this->actingAsAdmin();

        $neighborhoodsCount = Neighborhood::count();

        $response = $this->post(
            route('dashboard.neighborhoods.store'),
            Neighborhood::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $neighborhood = Neighborhood::all()->last();

        $this->assertEquals(Neighborhood::count(), $neighborhoodsCount + 1);

        $this->assertEquals($neighborhood->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_neighborhoods_edit_form()
    {
        $this->actingAsAdmin();

        $neighborhood = Neighborhood::factory()->create();

        $this->get(route('dashboard.neighborhoods.edit', $neighborhood))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_neighborhood()
    {
        $this->actingAsAdmin();

        $neighborhood = Neighborhood::factory()->create();

        $response = $this->put(
            route('dashboard.neighborhoods.update', $neighborhood),
            Neighborhood::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $neighborhood->refresh();

        $response->assertRedirect();

        $this->assertEquals($neighborhood->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_neighborhood()
    {
        $this->actingAsAdmin();

        $neighborhood = Neighborhood::factory()->create();

        $neighborhoodsCount = Neighborhood::count();

        $response = $this->delete(route('dashboard.neighborhoods.destroy', $neighborhood));

        $response->assertRedirect();

        $this->assertEquals(Neighborhood::count(), $neighborhoodsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_neighborhoods()
    {
        if (! $this->useSoftDeletes($model = Neighborhood::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Neighborhood::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.neighborhoods.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_neighborhood_details()
    {
        if (! $this->useSoftDeletes($model = Neighborhood::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $neighborhood = Neighborhood::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.neighborhoods.trashed.show', $neighborhood));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_neighborhood()
    {
        if (! $this->useSoftDeletes($model = Neighborhood::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $neighborhood = Neighborhood::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.neighborhoods.restore', $neighborhood));

        $response->assertRedirect();

        $this->assertNull($neighborhood->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_neighborhood()
    {
        if (! $this->useSoftDeletes($model = Neighborhood::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $neighborhood = Neighborhood::factory()->create(['deleted_at' => now()]);

        $neighborhoodCount = Neighborhood::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.neighborhoods.forceDelete', $neighborhood));

        $response->assertRedirect();

        $this->assertEquals(Neighborhood::withoutTrashed()->count(), $neighborhoodCount - 1);
    }

    /** @test */
    public function it_can_filter_neighborhoods_by_name()
    {
        $this->actingAsAdmin();

        Neighborhood::factory()->create([
            'name' => 'Foo',
        ]);

        Neighborhood::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.neighborhoods.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('neighborhoods.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
