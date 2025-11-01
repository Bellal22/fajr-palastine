<?php

namespace Database\Seeders;

use App\Models\InboundShipment;
use Illuminate\Database\Seeder;

class InboundShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InboundShipment::factory()->count(3)->create();
    }
}
