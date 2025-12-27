<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم اللوكيشن
            $table->text('description')->nullable(); // وصف اللوكيشن

            // ربط بالمنطقة
            $table->foreignId('region_id')
                ->constrained('regions')
                ->onDelete('cascade');

            // ربط بالبلوك/المندوب
            $table->foreignId('block_id')
                ->constrained('blocks')
                ->onDelete('cascade');

            // الإحداثيات
            $table->decimal('latitude', 10, 8); // خط العرض
            $table->decimal('longitude', 11, 8); // خط الطول

            // نوع اللوكيشن (اختياري)
            $table->enum('type', ['house', 'shelter', 'center', 'other'])
                ->default('other');

            // أيقونة اللوكيشن
            $table->string('icon_color')->default('#9C27B0'); // لون الأيقونة (بنفسجي)

            // معلومات إضافية
            $table->string('address')->nullable(); // العنوان
            $table->string('phone')->nullable(); // رقم هاتف

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}