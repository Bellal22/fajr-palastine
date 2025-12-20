<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maps', function (Blueprint $table) {
            $table->id();

            // معلومات أساسية عن الخريطة / المنطقة
            $table->string('name');                     // اسم داخلي للخريطة
            $table->string('title');                    // عنوان الخريطة الظاهر للمستخدم
            $table->text('description')->nullable();    // وصف الخريطة

            // إعدادات الخريطة الافتراضية
            $table->decimal('default_lat', 10, 8)->default(31.3547); // Khan Younis
            $table->decimal('default_lng', 11, 8)->default(34.3088);
            $table->integer('default_zoom')->default(13);

            // تفعيل / تعطيل الخريطة
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maps');
    }
}
