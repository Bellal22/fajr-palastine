<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\CouponType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_coupon_types()
    {
        $this->actingAsAdmin();

        CouponType::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.coupon_types.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_coupon_type_details()
    {
        $this->actingAsAdmin();

        $coupon_type = CouponType::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.coupon_types.show', $coupon_type))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_coupon_types_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.coupon_types.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_coupon_type()
    {
        $this->actingAsAdmin();

        $coupon_typesCount = CouponType::count();

        $response = $this->post(
            route('dashboard.coupon_types.store'),
            CouponType::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $coupon_type = CouponType::all()->last();

        $this->assertEquals(CouponType::count(), $coupon_typesCount + 1);

        $this->assertEquals($coupon_type->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_coupon_types_edit_form()
    {
        $this->actingAsAdmin();

        $coupon_type = CouponType::factory()->create();

        $this->get(route('dashboard.coupon_types.edit', $coupon_type))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_coupon_type()
    {
        $this->actingAsAdmin();

        $coupon_type = CouponType::factory()->create();

        $response = $this->put(
            route('dashboard.coupon_types.update', $coupon_type),
            CouponType::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $coupon_type->refresh();

        $response->assertRedirect();

        $this->assertEquals($coupon_type->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_coupon_type()
    {
        $this->actingAsAdmin();

        $coupon_type = CouponType::factory()->create();

        $coupon_typesCount = CouponType::count();

        $response = $this->delete(route('dashboard.coupon_types.destroy', $coupon_type));

        $response->assertRedirect();

        $this->assertEquals(CouponType::count(), $coupon_typesCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_coupon_types()
    {
        if (! $this->useSoftDeletes($model = CouponType::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        CouponType::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.coupon_types.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_coupon_type_details()
    {
        if (! $this->useSoftDeletes($model = CouponType::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $coupon_type = CouponType::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.coupon_types.trashed.show', $coupon_type));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_coupon_type()
    {
        if (! $this->useSoftDeletes($model = CouponType::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $coupon_type = CouponType::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.coupon_types.restore', $coupon_type));

        $response->assertRedirect();

        $this->assertNull($coupon_type->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_coupon_type()
    {
        if (! $this->useSoftDeletes($model = CouponType::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $coupon_type = CouponType::factory()->create(['deleted_at' => now()]);

        $coupon_typeCount = CouponType::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.coupon_types.forceDelete', $coupon_type));

        $response->assertRedirect();

        $this->assertEquals(CouponType::withoutTrashed()->count(), $coupon_typeCount - 1);
    }

    /** @test */
    public function it_can_filter_coupon_types_by_name()
    {
        $this->actingAsAdmin();

        CouponType::factory()->create([
            'name' => 'Foo',
        ]);

        CouponType::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.coupon_types.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('coupon_types.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
