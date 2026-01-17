<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Choose;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChooseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_chooses()
    {
        $this->actingAsAdmin();

        Choose::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.chooses.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_choose_details()
    {
        $this->actingAsAdmin();

        $choose = Choose::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.chooses.show', $choose))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_chooses_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.chooses.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_choose()
    {
        $this->actingAsAdmin();

        $choosesCount = Choose::count();

        $response = $this->post(
            route('dashboard.chooses.store'),
            Choose::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $choose = Choose::all()->last();

        $this->assertEquals(Choose::count(), $choosesCount + 1);

        $this->assertEquals($choose->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_chooses_edit_form()
    {
        $this->actingAsAdmin();

        $choose = Choose::factory()->create();

        $this->get(route('dashboard.chooses.edit', $choose))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_choose()
    {
        $this->actingAsAdmin();

        $choose = Choose::factory()->create();

        $response = $this->put(
            route('dashboard.chooses.update', $choose),
            Choose::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $choose->refresh();

        $response->assertRedirect();

        $this->assertEquals($choose->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_choose()
    {
        $this->actingAsAdmin();

        $choose = Choose::factory()->create();

        $choosesCount = Choose::count();

        $response = $this->delete(route('dashboard.chooses.destroy', $choose));

        $response->assertRedirect();

        $this->assertEquals(Choose::count(), $choosesCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_chooses()
    {
        if (! $this->useSoftDeletes($model = Choose::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Choose::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.chooses.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_choose_details()
    {
        if (! $this->useSoftDeletes($model = Choose::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $choose = Choose::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.chooses.trashed.show', $choose));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_choose()
    {
        if (! $this->useSoftDeletes($model = Choose::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $choose = Choose::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.chooses.restore', $choose));

        $response->assertRedirect();

        $this->assertNull($choose->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_choose()
    {
        if (! $this->useSoftDeletes($model = Choose::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $choose = Choose::factory()->create(['deleted_at' => now()]);

        $chooseCount = Choose::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.chooses.forceDelete', $choose));

        $response->assertRedirect();

        $this->assertEquals(Choose::withoutTrashed()->count(), $chooseCount - 1);
    }

    /** @test */
    public function it_can_filter_chooses_by_name()
    {
        $this->actingAsAdmin();

        Choose::factory()->create([
            'name' => 'Foo',
        ]);

        Choose::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.chooses.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('chooses.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
