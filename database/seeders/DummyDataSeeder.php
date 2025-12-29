<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingSeeder::class);
//        $this->call(UserSeeder::class);
        $this->call(FeedbackSeeder::class);
//        $this->call(FamilySeeder::class);
//        $this->call(SubCitySeeder::class);
//        $this->call(NeighborhoodSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(PersonSeeder::class);
        $this->call(PersonSeeder::class);
        $this->call(ComplaintSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(AreaResponsibleSeeder::class);
        $this->call(BlockSeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(InboundShipmentSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(MapSeeder::class);
        $this->call(ReadyPackageSeeder::class);
        $this->call(InternalPackageSeeder::class);
        $this->call(PackageContentSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(SubWarehouseSeeder::class);
        $this->call(OutboundShipmentSeeder::class);
        $this->call(OutboundShipmentItemSeeder::class);
        $this->call(CouponTypeSeeder::class);
        /*  The seeders of generated crud will set here: Don't remove this line  */
    }
}