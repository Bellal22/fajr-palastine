<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\InternalPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InternalPackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_internal_packages()
    {
        $this->actingAsAdmin();

        InternalPackage::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.internal_packages.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_internal_package_details()
    {
        $this->actingAsAdmin();

        $internal_package = InternalPackage::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.internal_packages.show', $internal_package))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_internal_packages_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.internal_packages.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_internal_package()
    {
        $this->actingAsAdmin();

        $internal_packagesCount = InternalPackage::count();

        $response = $this->post(
            route('dashboard.internal_packages.store'),
            InternalPackage::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $internal_package = InternalPackage::all()->last();

        $this->assertEquals(InternalPackage::count(), $internal_packagesCount + 1);

        $this->assertEquals($internal_package->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_internal_packages_edit_form()
    {
        $this->actingAsAdmin();

        $internal_package = InternalPackage::factory()->create();

        $this->get(route('dashboard.internal_packages.edit', $internal_package))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_internal_package()
    {
        $this->actingAsAdmin();

        $internal_package = InternalPackage::factory()->create();

        $response = $this->put(
            route('dashboard.internal_packages.update', $internal_package),
            InternalPackage::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $internal_package->refresh();

        $response->assertRedirect();

        $this->assertEquals($internal_package->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_internal_package()
    {
        $this->actingAsAdmin();

        $internal_package = InternalPackage::factory()->create();

        $internal_packagesCount = InternalPackage::count();

        $response = $this->delete(route('dashboard.internal_packages.destroy', $internal_package));

        $response->assertRedirect();

        $this->assertEquals(InternalPackage::count(), $internal_packagesCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_internal_packages()
    {
        if (! $this->useSoftDeletes($model = InternalPackage::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        InternalPackage::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.internal_packages.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_internal_package_details()
    {
        if (! $this->useSoftDeletes($model = InternalPackage::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $internal_package = InternalPackage::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.internal_packages.trashed.show', $internal_package));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_internal_package()
    {
        if (! $this->useSoftDeletes($model = InternalPackage::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $internal_package = InternalPackage::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.internal_packages.restore', $internal_package));

        $response->assertRedirect();

        $this->assertNull($internal_package->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_internal_package()
    {
        if (! $this->useSoftDeletes($model = InternalPackage::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $internal_package = InternalPackage::factory()->create(['deleted_at' => now()]);

        $internal_packageCount = InternalPackage::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.internal_packages.forceDelete', $internal_package));

        $response->assertRedirect();

        $this->assertEquals(InternalPackage::withoutTrashed()->count(), $internal_packageCount - 1);
    }

    /** @test */
    public function it_can_filter_internal_packages_by_name()
    {
        $this->actingAsAdmin();

        InternalPackage::factory()->create([
            'name' => 'Foo',
        ]);

        InternalPackage::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.internal_packages.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('internal_packages.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
