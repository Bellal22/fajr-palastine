<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_people()
    {
        $this->actingAsAdmin();

        Person::factory()->count(2)->create();

        $this->getJson(route('api.people.index'))
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
    public function test_people_select2_api()
    {
        Person::factory()->count(5)->create();

        $response = $this->getJson(route('api.people.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.people.select', ['selected_id' => 4]))
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
    public function it_can_display_the_person_details()
    {
        $this->actingAsAdmin();

        $person = Person::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.people.show', $person))
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
