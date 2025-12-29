<?php

namespace Database\Seeders;

use App\Models\CouponType;
use Illuminate\Database\Seeder;

class CouponTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CouponType::factory()->count(3)->create();
    }
}
