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
        if (!Schema::hasTable('rrm_batches')) {
            Schema::create('rrm_batches', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->date('delivery_date')->nullable();
                $table->foreignId('sub_warehouse_id')->nullable()->constrained('sub_warehouses')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->json('project_ids')->nullable();
                $table->json('project_quantities')->nullable();
                $table->unsignedInteger('beneficiaries_count')->default(0);
                $table->unsignedInteger('total_parcels')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('project_beneficiaries') && !Schema::hasColumn('project_beneficiaries', 'rrm_batch_id')) {
            Schema::table('project_beneficiaries', function (Blueprint $table) {
                $table->foreignId('rrm_batch_id')->nullable()->after('notes')->constrained('rrm_batches')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('project_beneficiaries') && Schema::hasColumn('project_beneficiaries', 'rrm_batch_id')) {
            Schema::table('project_beneficiaries', function (Blueprint $table) {
                $table->dropForeign(['rrm_batch_id']);
                $table->dropColumn('rrm_batch_id');
            });
        }

        Schema::dropIfExists('rrm_batches');
    }
};
