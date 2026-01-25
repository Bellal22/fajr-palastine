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
            ['type' => 'gender', 'name' => 'ذكر', 'slug' => 'ذكر', 'order' => 1],
            ['type' => 'gender', 'name' => 'أنثى', 'slug' => 'أنثى', 'order' => 2],

            // Employment Status
            ['type' => 'employment_status', 'name' => 'موظف', 'slug' => 'موظف', 'order' => 1],
            ['type' => 'employment_status', 'name' => 'عامل', 'slug' => 'عامل', 'order' => 2],
            ['type' => 'employment_status', 'name' => 'لا يعمل', 'slug' => 'لا يعمل', 'order' => 3],

            // Social Status
            ['type' => 'social_status', 'name' => 'أعزب', 'slug' => 'أعزب', 'order' => 1],
            ['type' => 'social_status', 'name' => 'متزوج', 'slug' => 'متزوج', 'order' => 2],
            ['type' => 'social_status', 'name' => 'متزوج بأكثر من واحدة', 'slug' => 'متزوج بأكثر من واحدة', 'order' => 3],
            ['type' => 'social_status', 'name' => 'مطلق', 'slug' => 'مطلق', 'order' => 4],
            ['type' => 'social_status', 'name' => 'أرمل', 'slug' => 'أرمل', 'order' => 5],

            // Housing Type
            ['type' => 'housing_type', 'name' => 'خيمة', 'slug' => 'خيمة', 'order' => 1],
            ['type' => 'housing_type', 'name' => 'زينكو', 'slug' => 'زينكو', 'order' => 2],
            ['type' => 'housing_type', 'name' => 'باطون', 'slug' => 'باطون', 'order' => 3],

            // Housing Damage Status
            ['type' => 'housing_damage_status', 'name' => 'كلي', 'slug' => 'كلي', 'order' => 1],
            ['type' => 'housing_damage_status', 'name' => 'جزئي', 'slug' => 'جزئي', 'order' => 2],
            ['type' => 'housing_damage_status', 'name' => 'غير متضرر', 'slug' => 'غير متضرر', 'order' => 3],

            // Relationship
            ['type' => 'relationship', 'name' => 'أخ', 'slug' => 'أخ', 'order' => 1],
            ['type' => 'relationship', 'name' => 'أخت', 'slug' => 'أخت', 'order' => 2],
            ['type' => 'relationship', 'name' => 'زوج', 'slug' => 'زوج', 'order' => 3],
            ['type' => 'relationship', 'name' => 'زوجة', 'slug' => 'زوجة', 'order' => 4],
            ['type' => 'relationship', 'name' => 'ابن', 'slug' => 'ابن', 'order' => 5],
            ['type' => 'relationship', 'name' => 'ابنة', 'slug' => 'ابنة', 'order' => 6],
            ['type' => 'relationship', 'name' => 'رب الأسرة', 'slug' => 'رب الأسرة', 'order' => 7],
        ];

        foreach ($data as $item) {
            DB::table('chooses')->updateOrInsert(
                ['type' => $item['type'], 'slug' => $item['slug']], // Check mainly by type and slug
                ['name' => $item['name'], 'order' => $item['order']]
            );
        }
    }
}
