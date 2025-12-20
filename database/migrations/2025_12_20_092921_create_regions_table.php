<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المنطقة
            $table->text('description')->nullable(); // وصف المنطقة
            $table->string('color')->default('#FF0000'); // لون المنطقة على الخريطة

            // ربط بمسؤول المنطقة
            $table->foreignId('area_responsible_id')
                ->nullable()
                ->constrained('area_responsibles')
                ->onDelete('set null');

            // الحدود الجغرافية (مصفوفة من الإحداثيات)
            // يتم حفظها كـ JSON بصيغة: [{"lat": 31.5, "lng": 34.5}, ...]
            $table->json('boundaries');

            // معلومات إضافية
            $table->decimal('center_lat', 10, 8)->nullable(); // مركز المنطقة - خط العرض
            $table->decimal('center_lng', 11, 8)->nullable(); // مركز المنطقة - خط الطول
            $table->boolean('is_active')->default(true); // هل المنطقة نشطة

            $table->timestamps();
            $table->softDeletes(); // للحذف الآمن
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regions');
    }
}