<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\SubCity;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubCityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_sub_cities()
    {
        $this->actingAsAdmin();

        SubCity::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.sub_cities.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_sub_city_details()
    {
        $this->actingAsAdmin();

        $sub_city = SubCity::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.sub_cities.show', $sub_city))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_sub_cities_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.sub_cities.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_sub_city()
    {
        $this->actingAsAdmin();

        $sub_citiesCount = SubCity::count();

        $response = $this->post(
            route('dashboard.sub_cities.store'),
            SubCity::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $sub_city = SubCity::all()->last();

        $this->assertEquals(SubCity::count(), $sub_citiesCount + 1);

        $this->assertEquals($sub_city->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_sub_cities_edit_form()
    {
        $this->actingAsAdmin();

        $sub_city = SubCity::factory()->create();

        $this->get(route('dashboard.sub_cities.edit', $sub_city))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_sub_city()
    {
        $this->actingAsAdmin();

        $sub_city = SubCity::factory()->create();

        $response = $this->put(
            route('dashboard.sub_cities.update', $sub_city),
            SubCity::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $sub_city->refresh();

        $response->assertRedirect();

        $this->assertEquals($sub_city->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_sub_city()
    {
        $this->actingAsAdmin();

        $sub_city = SubCity::factory()->create();

        $sub_citiesCount = SubCity::count();

        $response = $this->delete(route('dashboard.sub_cities.destroy', $sub_city));

        $response->assertRedirect();

        $this->assertEquals(SubCity::count(), $sub_citiesCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_sub_cities()
    {
        if (! $this->useSoftDeletes($model = SubCity::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        SubCity::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.sub_cities.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_sub_city_details()
    {
        if (! $this->useSoftDeletes($model = SubCity::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $sub_city = SubCity::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.sub_cities.trashed.show', $sub_city));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_sub_city()
    {
        if (! $this->useSoftDeletes($model = SubCity::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $sub_city = SubCity::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.sub_cities.restore', $sub_city));

        $response->assertRedirect();

        $this->assertNull($sub_city->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_sub_city()
    {
        if (! $this->useSoftDeletes($model = SubCity::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $sub_city = SubCity::factory()->create(['deleted_at' => now()]);

        $sub_cityCount = SubCity::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.sub_cities.forceDelete', $sub_city));

        $response->assertRedirect();

        $this->assertEquals(SubCity::withoutTrashed()->count(), $sub_cityCount - 1);
    }

    /** @test */
    public function it_can_filter_sub_cities_by_name()
    {
        $this->actingAsAdmin();

        SubCity::factory()->create([
            'name' => 'Foo',
        ]);

        SubCity::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.sub_cities.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('sub_cities.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
