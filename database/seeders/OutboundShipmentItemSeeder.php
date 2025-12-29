<?php

namespace Database\Seeders;

use App\Models\OutboundShipmentItem;
use Illuminate\Database\Seeder;

class OutboundShipmentItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OutboundShipmentItem::factory()->count(3)->create();
    }
}
