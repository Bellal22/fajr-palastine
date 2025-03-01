<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FamilyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_families()
    {
        $this->actingAsAdmin();

        Family::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.families.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_family_details()
    {
        $this->actingAsAdmin();

        $family = Family::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.families.show', $family))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_families_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.families.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_family()
    {
        $this->actingAsAdmin();

        $familiesCount = Family::count();

        $response = $this->post(
            route('dashboard.families.store'),
            Family::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $family = Family::all()->last();

        $this->assertEquals(Family::count(), $familiesCount + 1);

        $this->assertEquals($family->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_families_edit_form()
    {
        $this->actingAsAdmin();

        $family = Family::factory()->create();

        $this->get(route('dashboard.families.edit', $family))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_family()
    {
        $this->actingAsAdmin();

        $family = Family::factory()->create();

        $response = $this->put(
            route('dashboard.families.update', $family),
            Family::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $family->refresh();

        $response->assertRedirect();

        $this->assertEquals($family->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_family()
    {
        $this->actingAsAdmin();

        $family = Family::factory()->create();

        $familiesCount = Family::count();

        $response = $this->delete(route('dashboard.families.destroy', $family));

        $response->assertRedirect();

        $this->assertEquals(Family::count(), $familiesCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_families()
    {
        if (! $this->useSoftDeletes($model = Family::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Family::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.families.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_family_details()
    {
        if (! $this->useSoftDeletes($model = Family::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $family = Family::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.families.trashed.show', $family));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_family()
    {
        if (! $this->useSoftDeletes($model = Family::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $family = Family::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.families.restore', $family));

        $response->assertRedirect();

        $this->assertNull($family->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_family()
    {
        if (! $this->useSoftDeletes($model = Family::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $family = Family::factory()->create(['deleted_at' => now()]);

        $familyCount = Family::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.families.forceDelete', $family));

        $response->assertRedirect();

        $this->assertEquals(Family::withoutTrashed()->count(), $familyCount - 1);
    }

    /** @test */
    public function it_can_filter_families_by_name()
    {
        $this->actingAsAdmin();

        Family::factory()->create([
            'name' => 'Foo',
        ]);

        Family::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.families.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('families.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
