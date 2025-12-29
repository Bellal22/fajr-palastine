<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_projects()
    {
        $this->actingAsAdmin();

        Project::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.projects.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_project_details()
    {
        $this->actingAsAdmin();

        $project = Project::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.projects.show', $project))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_projects_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.projects.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_project()
    {
        $this->actingAsAdmin();

        $projectsCount = Project::count();

        $response = $this->post(
            route('dashboard.projects.store'),
            Project::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $project = Project::all()->last();

        $this->assertEquals(Project::count(), $projectsCount + 1);

        $this->assertEquals($project->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_projects_edit_form()
    {
        $this->actingAsAdmin();

        $project = Project::factory()->create();

        $this->get(route('dashboard.projects.edit', $project))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_project()
    {
        $this->actingAsAdmin();

        $project = Project::factory()->create();

        $response = $this->put(
            route('dashboard.projects.update', $project),
            Project::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $project->refresh();

        $response->assertRedirect();

        $this->assertEquals($project->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_project()
    {
        $this->actingAsAdmin();

        $project = Project::factory()->create();

        $projectsCount = Project::count();

        $response = $this->delete(route('dashboard.projects.destroy', $project));

        $response->assertRedirect();

        $this->assertEquals(Project::count(), $projectsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_projects()
    {
        if (! $this->useSoftDeletes($model = Project::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        Project::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.projects.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_project_details()
    {
        if (! $this->useSoftDeletes($model = Project::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $project = Project::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.projects.trashed.show', $project));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_project()
    {
        if (! $this->useSoftDeletes($model = Project::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $project = Project::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.projects.restore', $project));

        $response->assertRedirect();

        $this->assertNull($project->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_project()
    {
        if (! $this->useSoftDeletes($model = Project::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $project = Project::factory()->create(['deleted_at' => now()]);

        $projectCount = Project::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.projects.forceDelete', $project));

        $response->assertRedirect();

        $this->assertEquals(Project::withoutTrashed()->count(), $projectCount - 1);
    }

    /** @test */
    public function it_can_filter_projects_by_name()
    {
        $this->actingAsAdmin();

        Project::factory()->create([
            'name' => 'Foo',
        ]);

        Project::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.projects.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('projects.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
