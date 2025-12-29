<?php

namespace Database\Seeders;

use App\Models\ReadyPackage;
use Illuminate\Database\Seeder;

class ReadyPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReadyPackage::factory()->count(3)->create();
    }
}
