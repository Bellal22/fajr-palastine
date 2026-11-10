<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakePersonIdNullableInProjectBeneficiaries extends Migration
{
    public function up()
    {
        // استخدام SQL مباشر لضمان التعديل بدون الحاجة لمكتبات إضافية
        DB::statement('ALTER TABLE project_beneficiaries MODIFY person_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE project_beneficiaries MODIFY person_id BIGINT UNSIGNED NOT NULL');
    }
}
