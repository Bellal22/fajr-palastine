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
        Schema::table('inbound_shipments', function (Blueprint $table) {
            // إزالة الحقول القديمة التي لن نستخدمها
            $table->dropColumn(['name', 'description', 'weight', 'quantity']);

            // إضافة الحقول الجديدة
            $table->string('shipment_number')->unique()->after('supplier_id');
            $table->enum('inbound_type', ['ready_package', 'single_item'])
                ->default('single_item')
                ->comment('طرد جاهز أو صنف مفرد')
                ->after('shipment_number');
            $table->text('notes')->nullable()->after('inbound_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inbound_shipments', function (Blueprint $table) {
            $table->dropColumn(['shipment_number', 'inbound_type', 'notes']);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->float('weight')->nullable();
            $table->unsignedSmallInteger('quantity')->nullable();
        });
    }
};