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
        Schema::table('neighborhoods', function (Blueprint $table) {
            // 1. Clean up sub_city_id if it still exists
            $oldFkName = 'neighborhoods_sub_city_id_foreign';
            // Check legacy FK
            $oldFkExists = \Illuminate\Support\Facades\DB::select(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_NAME = 'neighborhoods'
                 AND CONSTRAINT_NAME = ?
                 AND TABLE_SCHEMA = DATABASE()",
                [$oldFkName]
            );

            if (!empty($oldFkExists)) {
                $table->dropForeign($oldFkName);
            }

            if (Schema::hasColumn('neighborhoods', 'sub_city_id')) {
                $table->dropColumn('sub_city_id');
            }

            // 2. Add city_id if not exists
            if (!Schema::hasColumn('neighborhoods', 'city_id')) {
                $table->foreignId('city_id')->after('name')->constrained('cities')->cascadeOnDelete();
            } else {
                // 3. If city_id exists, Ensure Foreign Key exists
                $newFkName = 'neighborhoods_city_id_foreign';
                $newFkExists = \Illuminate\Support\Facades\DB::select(
                    "SELECT CONSTRAINT_NAME
                     FROM information_schema.KEY_COLUMN_USAGE
                     WHERE TABLE_NAME = 'neighborhoods'
                     AND CONSTRAINT_NAME = ?
                     AND TABLE_SCHEMA = DATABASE()",
                    [$newFkName]
                );

                if (empty($newFkExists)) {
                    // constraint name convention assumed, specifying it explicitly is safer if we want to check it later
                    $table->foreign('city_id', $newFkName)->references('id')->on('cities')->cascadeOnDelete();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
            $table->foreignId('sub_city_id')->constrained('sub_cities')->cascadeOnDelete();
        });
    }
};
