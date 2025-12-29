<?php

namespace Database\Seeders;

use App\Models\OutboundShipment;
use Illuminate\Database\Seeder;

class OutboundShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OutboundShipment::factory()->count(3)->create();
    }
}
