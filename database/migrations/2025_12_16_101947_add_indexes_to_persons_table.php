<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // التحقق قبل إضافة الفهارس لتجنب الأخطاء إذا كانت موجودة باسم مختلف
        Schema::table('persons', function (Blueprint $table) {

            // 1. فهارس بسيطة مفقودة تماماً (أو لم تُنشأ باسمها):

            // فهرس للعمود المستخدم لتحديد المزامنة
            if (!Schema::hasIndex('persons', 'idx_api_synced_at')) {
                $table->index('api_synced_at', 'idx_api_synced_at');
            }

            // فهرس للحالة الاجتماعية (مستخدم في الإحصائيات)
            if (!Schema::hasIndex('persons', 'idx_social_status')) {
                $table->index('social_status', 'idx_social_status');
            }

            // فهرس للجنس
            if (!Schema::hasIndex('persons', 'idx_gender')) {
                $table->index('gender', 'idx_gender');
            }

            // فهرس لتاريخ الإنشاء
            if (!Schema::hasIndex('persons', 'idx_created_at')) {
                $table->index('created_at', 'idx_created_at');
            }

            // فهرس لتاريخ الميلاد
            if (!Schema::hasIndex('persons', 'idx_dob')) {
                $table->index('dob', 'idx_dob');
            }

            // ملاحظة: لم يتم تكرار idx_id_num أو idx_relationship لأنهما موجودان.

            // 2. الفهارس المركبة المفقودة (مهمة جداً لأداء الاستعلامات الإحصائية):

            // فهرس مركب للعلاقة ومسؤول المنطقة وحالة الموافقة (لتحديد أرباب الأسر المعتمدين لكل مسؤول)
            if (!Schema::hasIndex('persons', 'idx_rel_area_block')) {
                $table->index(['relative_id', 'area_responsible_id', 'block_id'], 'idx_rel_area_block');
            }

            // فهرس مركب لمسؤول المنطقة وحالة الموافقة
            if (!Schema::hasIndex('persons', 'idx_area_block')) {
                $table->index(['area_responsible_id', 'block_id'], 'idx_area_block');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            // حذف الفهارس التي تم إضافتها في up()
            if (Schema::hasIndex('persons', 'idx_api_synced_at')) {
                $table->dropIndex('idx_api_synced_at');
            }
            if (Schema::hasIndex('persons', 'idx_social_status')) {
                $table->dropIndex('idx_social_status');
            }
            if (Schema::hasIndex('persons', 'idx_gender')) {
                $table->dropIndex('idx_gender');
            }
            if (Schema::hasIndex('persons', 'idx_created_at')) {
                $table->dropIndex('idx_created_at');
            }
            if (Schema::hasIndex('persons', 'idx_dob')) {
                $table->dropIndex('idx_dob');
            }
            if (Schema::hasIndex('persons', 'idx_rel_area_block')) {
                $table->dropIndex('idx_rel_area_block');
            }
            if (Schema::hasIndex('persons', 'idx_area_block')) {
                $table->dropIndex('idx_area_block');
            }
        });
    }
};