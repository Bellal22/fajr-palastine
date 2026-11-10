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
        if (Schema::hasTable('complaints') && !Schema::hasColumn('complaints', 'complaint_type_id')) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->foreignId('complaint_type_id')->nullable()->after('id_num')->constrained('complaint_types')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('complaints') && Schema::hasColumn('complaints', 'complaint_type_id')) {
            Schema::table('complaints', function (Blueprint $table) {
                $table->dropForeign(['complaint_type_id']);
                $table->dropColumn('complaint_type_id');
            });
        }
    }
};
