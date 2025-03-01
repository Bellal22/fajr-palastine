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
        $cityIds = [];
        foreach ($cities as $key => $name) {
            $cityIds[$key] = DB::table('cities')->insertGetId([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Define sub-cities
        $subCities = [
            'northGaza' => [
                'jabalia' => 'جباليا',
                'beitLahia' => 'بيت لاهيا',
                'beitHanoun' => 'بيت حانون',
            ],
            'gaza' => [
                'gazaCity' => 'مدينة غزة',
                'zahra' => 'الزهراء',
                'maghraqa' => 'المغراقة',
            ],
            'middleArea' => [
                'breij' => 'البريج',
                'nusairat' => 'النصيرات',
                'maghazi' => 'المغازي',
                'zawaida' => 'الزوايدة',
                'deirBalh' => 'دير البلح',
            ],
            'khanYounis' => [
                'khanYounisCity' => 'مدينة خان يونس',
            ],
            'rafah' => [
                'rafahCity' => 'مدينة رفح',
            ],
        ];

        // Insert sub-cities
        $subCityIds = [];
        foreach ($subCities as $cityKey => $subs) {
            foreach ($subs as $key => $name) {
                $subCityIds[$key] = DB::table('sub_cities')->insertGetId([
                    'city_id' => $cityIds[$cityKey],
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Define neighborhoods
        $neighborhoods = [
            'jabalia' => [
                'جباليا البلد', 'تل الزعتر', 'النزلة', 'مخيم جباليا', 'الفالوجا', 'الخزندار', 'الكرامة', 'حي الندى', 'شارع السكة', 'عزبة عبد ربه', 'الصفطاوي'
            ],
            'beitLahia' => [
                'العطاطرة', 'السلاطين', 'الصفطاوي', 'مشروع بيت لاهيا', 'الشيخ زايد', 'الفيروز'
            ],
            'beitHanoun' => [
                'الزيتون', 'النصر', 'السلام', 'المنطار', 'المنطقة الزراعية'
            ],
            'gazaCity' => [
                'الرمال الجنوبي', 'الرمال الشمالي', 'الصبرة', 'التفاح', 'الزيتون', 'الدرج', 'الشيخ رضوان', 'النصر', 'الكرامة', 'تل الهوى',
                'اليرموك', 'الجلاء', 'الشجاعية - التركمان', 'الشجاعية - اجديدة', 'الشجاعية - المنطار', 'الشجاعية - القبة', 'الميناء', 'البستان',
                'الصمود', 'الأمل'
            ],
            'zahra' => [
                'الحي الأول', 'الحي الثاني', 'الحي الثالث', 'النخيل', 'الفروسية'
            ],
            'maghraqa' => [
                'أبو مدين', 'الزيتون', 'الكرامة'
            ],
            'breij' => [
                'المخيم القديم', 'المخيم الجديد', 'الصفطاوي', 'الزيتون'
            ],
            'nusairat' => [
                'بلوك 1 إلى بلوك 12', 'المخيم الجديد', 'الزهور', 'الزيتون', 'الإسكان الأحمر', 'الأمل', 'منطقة الزوايدة الشرقية'
            ],
            'maghazi' => [
                'المغازي الجنوبي', 'المغازي الشمالي'
            ],
            'zawaida' => [
                'أبو عبيدة', 'المقوسي', 'العايدي', 'المواصي'
            ],
            'deirBalh' => [
                'البلد', 'الشهداء', 'المزرعة', 'المنشية', 'البحير', 'مخيم دير البلح'
            ],
            'khanYounisCity' => [
                'وسط البلد', 'السطر الغربي', 'السطر الشرقي', 'المحطة', 'الكتيبة', 'البطن السمين', 'المعسكر', 'المشروع', 'مدينة حمد',
                'المواصي', 'القرارة', 'شرق خانيونس', 'جورة اللوت', 'الشيخ ناصر', 'معن', 'حي المنارة', 'دوار زعرب', 'ميراج', 'الأوروبي', 'الفخاري', 'مصبح'
            ],
            'rafahCity' => [
                'تل السلطان', 'يبنا', 'البرازيل', 'الجنينة', 'الشعوث', 'السلام', 'الصفا', 'مخيم رفح', 'المواصي', 'أبو حلاوة',
                'خربة العدس', 'العودة', 'حي الزهور', 'الإسكان البرازيلي', 'حي الشابورة', 'مشروع رفح', 'دوار زعرب', 'البركة'
            ],
        ];

        // Insert neighborhoods
        foreach ($neighborhoods as $subCityKey => $neighborhoodList) {
            foreach ($neighborhoodList as $name) {
                DB::table('neighborhoods')->insert([
                    'sub_city_id' => $subCityIds[$subCityKey],
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
