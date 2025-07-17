<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AreaResponsible; // تأكد إن الموديل موجود أو أنشئه لو مش موجود

class AreaResponsibleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $responsiblePersons = [
            'إيهاب العبادلة',
            'محمد حمتو الاغا',
            'عبدالرؤوف الاسطل',
            'ياسين جابر',
            'مهند الاسطل',
            'طارق الاسطل',
            'عبدالحي ابونمر',
            'ابوسليمان وادي',
            'نسرين ابوالحن',
            'نهيل اللحام',
            'يوسف صالح اللحام',
            'علي النجار',
            'ابونضال وادي',
            'ابوصالح زعرب',
            'ابوخليل الشاعر',
            'ابوعثمان القصاص',
        ];

        foreach ($responsiblePersons as $name) {
            AreaResponsible::create(['name' => $name]);
        }
    }
}
