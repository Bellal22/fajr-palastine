<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\AreaResponsible;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AreaResponsibleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_area_responsibles()
    {
        $this->actingAsAdmin();

        AreaResponsible::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.area_responsibles.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_area_responsible_details()
    {
        $this->actingAsAdmin();

        $area_responsible = AreaResponsible::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.area_responsibles.show', $area_responsible))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_area_responsibles_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.area_responsibles.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_area_responsible()
    {
        $this->actingAsAdmin();

        $area_responsiblesCount = AreaResponsible::count();

        $response = $this->post(
            route('dashboard.area_responsibles.store'),
            AreaResponsible::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $area_responsible = AreaResponsible::all()->last();

        $this->assertEquals(AreaResponsible::count(), $area_responsiblesCount + 1);

        $this->assertEquals($area_responsible->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_area_responsibles_edit_form()
    {
        $this->actingAsAdmin();

        $area_responsible = AreaResponsible::factory()->create();

        $this->get(route('dashboard.area_responsibles.edit', $area_responsible))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_area_responsible()
    {
        $this->actingAsAdmin();

        $area_responsible = AreaResponsible::factory()->create();

        $response = $this->put(
            route('dashboard.area_responsibles.update', $area_responsible),
            AreaResponsible::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $area_responsible->refresh();

        $response->assertRedirect();

        $this->assertEquals($area_responsible->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_area_responsible()
    {
        $this->actingAsAdmin();

        $area_responsible = AreaResponsible::factory()->create();

        $area_responsiblesCount = AreaResponsible::count();

        $response = $this->delete(route('dashboard.area_responsibles.destroy', $area_responsible));

        $response->assertRedirect();

        $this->assertEquals(AreaResponsible::count(), $area_responsiblesCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_area_responsibles()
    {
        if (! $this->useSoftDeletes($model = AreaResponsible::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        AreaResponsible::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.area_responsibles.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_area_responsible_details()
    {
        if (! $this->useSoftDeletes($model = AreaResponsible::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $area_responsible = AreaResponsible::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.area_responsibles.trashed.show', $area_responsible));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_area_responsible()
    {
        if (! $this->useSoftDeletes($model = AreaResponsible::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $area_responsible = AreaResponsible::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.area_responsibles.restore', $area_responsible));

        $response->assertRedirect();

        $this->assertNull($area_responsible->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_area_responsible()
    {
        if (! $this->useSoftDeletes($model = AreaResponsible::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $area_responsible = AreaResponsible::factory()->create(['deleted_at' => now()]);

        $area_responsibleCount = AreaResponsible::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.area_responsibles.forceDelete', $area_responsible));

        $response->assertRedirect();

        $this->assertEquals(AreaResponsible::withoutTrashed()->count(), $area_responsibleCount - 1);
    }

    /** @test */
    public function it_can_filter_area_responsibles_by_name()
    {
        $this->actingAsAdmin();

        AreaResponsible::factory()->create([
            'name' => 'Foo',
        ]);

        AreaResponsible::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.area_responsibles.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('area_responsibles.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
