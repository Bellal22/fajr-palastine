<?php

namespace Database\Seeders;

use App\Models\NeedRequest;
use Illuminate\Database\Seeder;

class NeedRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NeedRequest::factory()->count(3)->create();
    }
}
