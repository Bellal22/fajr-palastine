<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNeedRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // جدول إعدادات طلب الاحتياج للمشرفين
        Schema::create('need_request_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
            $table->unique('supervisor_id');
        });

        // جدول المشاريع المفعلة لطلبات الاحتياج
        Schema::create('need_request_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique('project_id');
        });

        // جدول طلبات الاحتياج الرئيسي
        Schema::create('need_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // جدول عناصر طلب الاحتياج (أرقام الهويات)
        Schema::create('need_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('need_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['need_request_id', 'person_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('need_request_items');
        Schema::dropIfExists('need_requests');
        Schema::dropIfExists('need_request_projects');
        Schema::dropIfExists('need_request_settings');
    }
}
