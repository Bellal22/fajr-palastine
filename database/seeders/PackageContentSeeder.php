<?php

namespace Database\Seeders;

use App\Models\PackageContent;
use Illuminate\Database\Seeder;

class PackageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PackageContent::factory()->count(3)->create();
    }
}
