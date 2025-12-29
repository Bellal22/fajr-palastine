<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\PackageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PackageContentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_package_contents()
    {
        $this->actingAsAdmin();

        PackageContent::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.package_contents.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_package_content_details()
    {
        $this->actingAsAdmin();

        $package_content = PackageContent::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.package_contents.show', $package_content))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_package_contents_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.package_contents.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_package_content()
    {
        $this->actingAsAdmin();

        $package_contentsCount = PackageContent::count();

        $response = $this->post(
            route('dashboard.package_contents.store'),
            PackageContent::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $package_content = PackageContent::all()->last();

        $this->assertEquals(PackageContent::count(), $package_contentsCount + 1);

        $this->assertEquals($package_content->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_package_contents_edit_form()
    {
        $this->actingAsAdmin();

        $package_content = PackageContent::factory()->create();

        $this->get(route('dashboard.package_contents.edit', $package_content))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_package_content()
    {
        $this->actingAsAdmin();

        $package_content = PackageContent::factory()->create();

        $response = $this->put(
            route('dashboard.package_contents.update', $package_content),
            PackageContent::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $package_content->refresh();

        $response->assertRedirect();

        $this->assertEquals($package_content->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_package_content()
    {
        $this->actingAsAdmin();

        $package_content = PackageContent::factory()->create();

        $package_contentsCount = PackageContent::count();

        $response = $this->delete(route('dashboard.package_contents.destroy', $package_content));

        $response->assertRedirect();

        $this->assertEquals(PackageContent::count(), $package_contentsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_package_contents()
    {
        if (! $this->useSoftDeletes($model = PackageContent::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        PackageContent::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.package_contents.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_package_content_details()
    {
        if (! $this->useSoftDeletes($model = PackageContent::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $package_content = PackageContent::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.package_contents.trashed.show', $package_content));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_package_content()
    {
        if (! $this->useSoftDeletes($model = PackageContent::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $package_content = PackageContent::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.package_contents.restore', $package_content));

        $response->assertRedirect();

        $this->assertNull($package_content->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_package_content()
    {
        if (! $this->useSoftDeletes($model = PackageContent::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $package_content = PackageContent::factory()->create(['deleted_at' => now()]);

        $package_contentCount = PackageContent::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.package_contents.forceDelete', $package_content));

        $response->assertRedirect();

        $this->assertEquals(PackageContent::withoutTrashed()->count(), $package_contentCount - 1);
    }

    /** @test */
    public function it_can_filter_package_contents_by_name()
    {
        $this->actingAsAdmin();

        PackageContent::factory()->create([
            'name' => 'Foo',
        ]);

        PackageContent::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.package_contents.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('package_contents.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
