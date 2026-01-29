<?php

namespace Database\Seeders;

use App\Models\GameWinning;
use Illuminate\Database\Seeder;

class GameWinningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GameWinning::factory()->count(3)->create();
    }
}
