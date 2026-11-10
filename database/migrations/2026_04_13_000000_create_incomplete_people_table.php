<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncompletePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incomplete_people', function (Blueprint $table) {
            $table->id();
            // الحقول الأساسية
            $table->string('id_num')->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->string('family_name')->nullable();
            $table->string('relationship')->nullable();
            $table->string('relative_id')->nullable()->index(); // لرب الأسرة في السكراب
            
            // المعلومات الاجتماعية
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('social_status')->nullable();
            $table->integer('relatives_count')->nullable();
            
            // التواصل والسكن
            $table->string('phone')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->string('original_governorate')->nullable();
            $table->string('city')->nullable();
            $table->string('current_city')->nullable();
            $table->string('neighborhood')->nullable();
            
            // الربط الإداري
            $table->foreignId('block_id')->nullable()->constrained('blocks')->nullOnDelete();
            $table->foreignId('area_responsible_id')->nullable()->constrained('area_responsibles')->nullOnDelete();
            
            // الحالة الصحية والمالية
            $table->boolean('has_condition')->default(false);
            $table->text('condition_description')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('income_level')->nullable();
            $table->string('housing_type')->nullable();
            $table->string('housing_damage_status')->nullable();
            
            // إضافات التطبيق
            $table->boolean('is_frozen')->default(false);
            $table->string('passkey')->nullable();
            $table->timestamp('api_synced_at')->nullable();
            $table->string('api_sync_status')->nullable();
            $table->text('api_sync_error')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });

        // إضافة حقل الربط في جدول الكوبونات
        Schema::table('project_beneficiaries', function (Blueprint $table) {
            $table->foreignId('incomplete_person_id')->after('person_id')->nullable()->constrained('incomplete_people')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_beneficiaries', function (Blueprint $table) {
            $table->dropForeign(['incomplete_person_id']);
            $table->dropColumn('incomplete_person_id');
        });
        Schema::dropIfExists('incomplete_people');
    }
}
