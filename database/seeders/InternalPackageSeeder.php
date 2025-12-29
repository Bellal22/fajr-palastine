<?php

namespace Database\Seeders;

use App\Models\InternalPackage;
use Illuminate\Database\Seeder;

class InternalPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InternalPackage::factory()->count(3)->create();
    }
}
