<?php

namespace Database\Seeders;

use App\Models\SubWarehouse;
use Illuminate\Database\Seeder;

class SubWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubWarehouse::factory()->count(3)->create();
    }
}
