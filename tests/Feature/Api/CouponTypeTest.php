<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\CouponType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_coupon_types()
    {
        $this->actingAsAdmin();

        CouponType::factory()->count(2)->create();

        $this->getJson(route('api.coupon_types.index'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    /** @test */
    public function test_coupon_types_select2_api()
    {
        CouponType::factory()->count(5)->create();

        $response = $this->getJson(route('api.coupon_types.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.coupon_types.select', ['selected_id' => 4]))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 4);

        $this->assertCount(5, $response->json('data'));
    }

    /** @test */
    public function it_can_display_the_coupon_type_details()
    {
        $this->actingAsAdmin();

        $coupon_type = CouponType::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.coupon_types.show', $coupon_type))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                ],
            ]);

        $this->assertEquals($response->json('data.name'), 'Foo');
    }
}
