<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\GameWinning;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameWinningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_all_game_winnings()
    {
        $this->actingAsAdmin();

        GameWinning::factory()->count(2)->create();

        $this->getJson(route('api.game_winnings.index'))
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
    public function test_game_winnings_select2_api()
    {
        GameWinning::factory()->count(5)->create();

        $response = $this->getJson(route('api.game_winnings.select'))
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'text'],
                ],
            ]);

        $this->assertEquals($response->json('data.0.id'), 1);

        $this->assertCount(5, $response->json('data'));

        $response = $this->getJson(route('api.game_winnings.select', ['selected_id' => 4]))
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
    public function it_can_display_the_game_winning_details()
    {
        $this->actingAsAdmin();

        $game_winning = GameWinning::factory(['name' => 'Foo'])->create();

        $response = $this->getJson(route('api.game_winnings.show', $game_winning))
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
