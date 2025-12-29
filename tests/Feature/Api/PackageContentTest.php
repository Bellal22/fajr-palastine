<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\PackageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PackageContentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_package_contents()
    {
        $this->actingAsAdmin();

        PackageContent::factory()->count(2)->create();

        $this->getJson(route('api.package_contents.index'))
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
    public function test_package_contents_select2_api()
    {
        PackageContent::factory()->count(5)->create();

        $response = $this->getJson(route('api.package_contents.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.package_contents.select', ['selected_id' => 4]))
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
    public function it_can_display_the_package_content_details()
    {
        $this->actingAsAdmin();

        $package_content = PackageContent::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.package_contents.show', $package_content))
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
