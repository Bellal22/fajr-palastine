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
        Schema::table('items', function (Blueprint $table) {
            // إزالة الحقول غير المستخدمة
            $table->dropColumn(['package', 'type']);

            // تأكد من وجود الحقول الأساسية
            // name, description, weight, quantity موجودة بالفعل

            // إضافة علاقة مع inbound_shipments
            $table->foreignId('inbound_shipment_id')
                ->nullable()
                ->after('id')
                ->constrained('inbound_shipments')
                ->nullOnDelete();

            // تعديل quantity ليصبح إجباري
            $table->unsignedInteger('quantity')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['inbound_shipment_id']);
            $table->dropColumn('inbound_shipment_id');
            $table->boolean('package')->default(0);
            $table->unsignedTinyInteger('type')->nullable();
        });;
    }
};