<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\ReadyPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReadyPackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_ready_packages()
    {
        $this->actingAsAdmin();

        ReadyPackage::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.ready_packages.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_ready_package_details()
    {
        $this->actingAsAdmin();

        $ready_package = ReadyPackage::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.ready_packages.show', $ready_package))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_ready_packages_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.ready_packages.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_ready_package()
    {
        $this->actingAsAdmin();

        $ready_packagesCount = ReadyPackage::count();

        $response = $this->post(
            route('dashboard.ready_packages.store'),
            ReadyPackage::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $ready_package = ReadyPackage::all()->last();

        $this->assertEquals(ReadyPackage::count(), $ready_packagesCount + 1);

        $this->assertEquals($ready_package->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_ready_packages_edit_form()
    {
        $this->actingAsAdmin();

        $ready_package = ReadyPackage::factory()->create();

        $this->get(route('dashboard.ready_packages.edit', $ready_package))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_ready_package()
    {
        $this->actingAsAdmin();

        $ready_package = ReadyPackage::factory()->create();

        $response = $this->put(
            route('dashboard.ready_packages.update', $ready_package),
            ReadyPackage::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $ready_package->refresh();

        $response->assertRedirect();

        $this->assertEquals($ready_package->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_ready_package()
    {
        $this->actingAsAdmin();

        $ready_package = ReadyPackage::factory()->create();

        $ready_packagesCount = ReadyPackage::count();

        $response = $this->delete(route('dashboard.ready_packages.destroy', $ready_package));

        $response->assertRedirect();

        $this->assertEquals(ReadyPackage::count(), $ready_packagesCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_ready_packages()
    {
        if (! $this->useSoftDeletes($model = ReadyPackage::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        ReadyPackage::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.ready_packages.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_ready_package_details()
    {
        if (! $this->useSoftDeletes($model = ReadyPackage::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $ready_package = ReadyPackage::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.ready_packages.trashed.show', $ready_package));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_ready_package()
    {
        if (! $this->useSoftDeletes($model = ReadyPackage::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $ready_package = ReadyPackage::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.ready_packages.restore', $ready_package));

        $response->assertRedirect();

        $this->assertNull($ready_package->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_ready_package()
    {
        if (! $this->useSoftDeletes($model = ReadyPackage::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $ready_package = ReadyPackage::factory()->create(['deleted_at' => now()]);

        $ready_packageCount = ReadyPackage::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.ready_packages.forceDelete', $ready_package));

        $response->assertRedirect();

        $this->assertEquals(ReadyPackage::withoutTrashed()->count(), $ready_packageCount - 1);
    }

    /** @test */
    public function it_can_filter_ready_packages_by_name()
    {
        $this->actingAsAdmin();

        ReadyPackage::factory()->create([
            'name' => 'Foo',
        ]);

        ReadyPackage::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.ready_packages.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('ready_packages.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
