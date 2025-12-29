<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\ReadyPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReadyPackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_ready_packages()
    {
        $this->actingAsAdmin();

        ReadyPackage::factory()->count(2)->create();

        $this->getJson(route('api.ready_packages.index'))
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
    public function test_ready_packages_select2_api()
    {
        ReadyPackage::factory()->count(5)->create();

        $response = $this->getJson(route('api.ready_packages.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.ready_packages.select', ['selected_id' => 4]))
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
    public function it_can_display_the_ready_package_details()
    {
        $this->actingAsAdmin();

        $ready_package = ReadyPackage::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.ready_packages.show', $ready_package))
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
