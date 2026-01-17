<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChooseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Gender
            ['type' => 'gender', 'name' => 'ذكر', 'slug' => 'male', 'order' => 1],
            ['type' => 'gender', 'name' => 'أنثى', 'slug' => 'female', 'order' => 2],

            // Employment Status
            ['type' => 'employment_status', 'name' => 'موظف', 'slug' => 'employee', 'order' => 1],
            ['type' => 'employment_status', 'name' => 'عامل', 'slug' => 'worker', 'order' => 2],
            ['type' => 'employment_status', 'name' => 'لا يعمل', 'slug' => 'unemployed', 'order' => 3],

            // Social Status
            ['type' => 'social_status', 'name' => 'أعزب', 'slug' => 'single', 'order' => 1],
            ['type' => 'social_status', 'name' => 'متزوج', 'slug' => 'married', 'order' => 2],
            ['type' => 'social_status', 'name' => 'متزوج بأكثر من واحدة', 'slug' => 'polygamous', 'order' => 3],
            ['type' => 'social_status', 'name' => 'مطلق', 'slug' => 'divorced', 'order' => 4],
            ['type' => 'social_status', 'name' => 'أرمل', 'slug' => 'widowed', 'order' => 5],

            // Housing Type
            ['type' => 'housing_type', 'name' => 'خيمة', 'slug' => 'tent', 'order' => 1],
            ['type' => 'housing_type', 'name' => 'زينكو', 'slug' => 'zinc', 'order' => 2],
            ['type' => 'housing_type', 'name' => 'باطون', 'slug' => 'concrete', 'order' => 3],

            // Housing Damage Status (Correcting the slug to match Enum if possible, or logical English)
            ['type' => 'housing_damage_status', 'name' => 'كلي', 'slug' => 'total', 'order' => 1],
            ['type' => 'housing_damage_status', 'name' => 'جزئي', 'slug' => 'partial', 'order' => 2],
            ['type' => 'housing_damage_status', 'name' => 'غير متضرر', 'slug' => 'notAffected', 'order' => 3],
            
            // Relationship (Adding this as it's often needed alongside social status)
            ['type' => 'relationship', 'name' => 'أخ', 'slug' => 'brother', 'order' => 1],
            ['type' => 'relationship', 'name' => 'أخت', 'slug' => 'sister', 'order' => 2],
            ['type' => 'relationship', 'name' => 'زوج', 'slug' => 'husband', 'order' => 3],
            ['type' => 'relationship', 'name' => 'زوجة', 'slug' => 'wife', 'order' => 4],
            ['type' => 'relationship', 'name' => 'ابن', 'slug' => 'son', 'order' => 5],
            ['type' => 'relationship', 'name' => 'ابنة', 'slug' => 'daughter', 'order' => 6],
        ];

        foreach ($data as $item) {
            DB::table('chooses')->updateOrInsert(
                ['type' => $item['type'], 'slug' => $item['slug']], // Check mainly by type and slug
                ['name' => $item['name'], 'order' => $item['order']]
            );
        }
    }
}
