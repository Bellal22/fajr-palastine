<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\InternalPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InternalPackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_internal_packages()
    {
        $this->actingAsAdmin();

        InternalPackage::factory()->count(2)->create();

        $this->getJson(route('api.internal_packages.index'))
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
    public function test_internal_packages_select2_api()
    {
        InternalPackage::factory()->count(5)->create();

        $response = $this->getJson(route('api.internal_packages.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.internal_packages.select', ['selected_id' => 4]))
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
    public function it_can_display_the_internal_package_details()
    {
        $this->actingAsAdmin();

        $internal_package = InternalPackage::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.internal_packages.show', $internal_package))
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
