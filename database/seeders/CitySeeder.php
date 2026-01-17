<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run()
    {
        // Define the cities
        $cities = [
            'northGaza' => 'شمال غزة',
            'gaza' => 'غزة',
            'middleArea' => 'الوسطى',
            'khanYounis' => 'خان يونس',
            'rafah' => 'رفح',
        ];

        // Insert cities
        foreach ($cities as $key => $name) {
            DB::table('cities')->updateOrInsert(
                ['name' => $name], // Use name as unique identifier to avoid duplicates
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
