<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_people()
    {
        $this->actingAsAdmin();

        Person::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.people.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_person_details()
    {
        $this->actingAsAdmin();

        $person = Person::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.people.show', $person))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_people_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.people.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_person()
    {
        $this->actingAsAdmin();

        $peopleCount = Person::count();

        $response = $this->post(
            route('dashboard.people.store'),
            Person::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $person = Person::all()->last();

        $this->assertEquals(Person::count(), $peopleCount + 1);

        $this->assertEquals($person->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_people_edit_form()
    {
        $this->actingAsAdmin();

        $person = Person::factory()->create();

        $this->get(route('dashboard.people.edit', $person))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_person()
    {
        $this->actingAsAdmin();

        $person = Person::factory()->create();

        $response = $this->put(
            route('dashboard.people.update', $person),
            Person::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $person->refresh();

        $response->assertRedirect();

        $this->assertEquals($person->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_person()
    {
        $this->actingAsAdmin();

        $person = Person::factory()->create();

        $peopleCount = Person::count();

        $response = $this->delete(route('dashboard.people.destroy', $person));

        $response->assertRedirect();

        $this->assertEquals(Person::count(), $peopleCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_people()
    {
        if (! $this->useSoftDeletes($model = Person::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Person::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.people.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_person_details()
    {
        if (! $this->useSoftDeletes($model = Person::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $person = Person::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.people.trashed.show', $person));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_person()
    {
        if (! $this->useSoftDeletes($model = Person::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $person = Person::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.people.restore', $person));

        $response->assertRedirect();

        $this->assertNull($person->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_person()
    {
        if (! $this->useSoftDeletes($model = Person::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $person = Person::factory()->create(['deleted_at' => now()]);

        $personCount = Person::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.people.forceDelete', $person));

        $response->assertRedirect();

        $this->assertEquals(Person::withoutTrashed()->count(), $personCount - 1);
    }

    /** @test */
    public function it_can_filter_people_by_name()
    {
        $this->actingAsAdmin();

        Person::factory()->create([
            'name' => 'Foo',
        ]);

        Person::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.people.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('people.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
