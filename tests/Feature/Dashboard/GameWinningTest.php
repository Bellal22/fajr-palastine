<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\GameWinning;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameWinningTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_a_list_of_game_winnings()
    {
        $this->actingAsAdmin();

        GameWinning::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.game_winnings.index'))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_the_game_winning_details()
    {
        $this->actingAsAdmin();

        $game_winning = GameWinning::factory()->create(['name' => 'Foo']);

        $this->get(route('dashboard.game_winnings.show', $game_winning))
            ->assertSuccessful()
            ->assertSee('Foo');
    }

    /** @test */
    public function it_can_display_game_winnings_create_form()
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard.game_winnings.create'))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_create_a_new_game_winning()
    {
        $this->actingAsAdmin();

        $game_winningsCount = GameWinning::count();

        $response = $this->post(
            route('dashboard.game_winnings.store'),
            GameWinning::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $response->assertRedirect();

        $game_winning = GameWinning::all()->last();

        $this->assertEquals(GameWinning::count(), $game_winningsCount + 1);

        $this->assertEquals($game_winning->name, 'Foo');
    }

    /** @test */
    public function it_can_display_the_game_winnings_edit_form()
    {
        $this->actingAsAdmin();

        $game_winning = GameWinning::factory()->create();

        $this->get(route('dashboard.game_winnings.edit', $game_winning))
            ->assertSuccessful();
    }

    /** @test */
    public function it_can_update_the_game_winning()
    {
        $this->actingAsAdmin();

        $game_winning = GameWinning::factory()->create();

        $response = $this->put(
            route('dashboard.game_winnings.update', $game_winning),
            GameWinning::factory()->raw([
                'name' => 'Foo'
            ])
        );

        $game_winning->refresh();

        $response->assertRedirect();

        $this->assertEquals($game_winning->name, 'Foo');
    }

    /** @test */
    public function it_can_delete_the_game_winning()
    {
        $this->actingAsAdmin();

        $game_winning = GameWinning::factory()->create();

        $game_winningsCount = GameWinning::count();

        $response = $this->delete(route('dashboard.game_winnings.destroy', $game_winning));

        $response->assertRedirect();

        $this->assertEquals(GameWinning::count(), $game_winningsCount - 1);
    }

    /** @test */
    public function it_can_display_trashed_game_winnings()
    {
        if (! $this->useSoftDeletes($model = GameWinning::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        GameWinning::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.game_winnings.trashed'));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_display_trashed_game_winning_details()
    {
        if (! $this->useSoftDeletes($model = GameWinning::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $game_winning = GameWinning::factory()->create(['deleted_at' => now(), 'name' => 'Ahmed']);

        $this->actingAsAdmin();

        $response = $this->get(route('dashboard.game_winnings.trashed.show', $game_winning));

        $response->assertSuccessful();

        $response->assertSee('Ahmed');
    }

    /** @test */
    public function it_can_restore_deleted_game_winning()
    {
        if (! $this->useSoftDeletes($model = GameWinning::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $game_winning = GameWinning::factory()->create(['deleted_at' => now()]);

        $this->actingAsAdmin();

        $response = $this->post(route('dashboard.game_winnings.restore', $game_winning));

        $response->assertRedirect();

        $this->assertNull($game_winning->refresh()->deleted_at);
    }

    /** @test */
    public function it_can_force_delete_game_winning()
    {
        if (! $this->useSoftDeletes($model = GameWinning::class)) {
            $this->markTestSkipped("The '$model' doesn't use soft deletes trait.");
        }

        $game_winning = GameWinning::factory()->create(['deleted_at' => now()]);

        $game_winningCount = GameWinning::withTrashed()->count();

        $this->actingAsAdmin();

        $response = $this->delete(route('dashboard.game_winnings.forceDelete', $game_winning));

        $response->assertRedirect();

        $this->assertEquals(GameWinning::withoutTrashed()->count(), $game_winningCount - 1);
    }

    /** @test */
    public function it_can_filter_game_winnings_by_name()
    {
        $this->actingAsAdmin();

        GameWinning::factory()->create([
            'name' => 'Foo',
        ]);

        GameWinning::factory()->create([
            'name' => 'Bar',
        ]);

        $this->get(route('dashboard.game_winnings.index', [
            'name' => 'Fo',
        ]))
            ->assertSuccessful()
            ->assertSee(trans('game_winnings.filter'))
            ->assertSee('Foo')
            ->assertDontSee('Bar');
    }
}
