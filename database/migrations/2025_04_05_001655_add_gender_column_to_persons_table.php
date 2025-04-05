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
        if (!Schema::hasColumn('persons', 'gender')) {
            Schema::table('persons', function (Blueprint $table) {
                $table->enum('gender', ['ذكر', 'أنثى', 'غير محدد'])
                    ->default('غير محدد')
                    ->after('family_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('persons', 'gender')) {
            Schema::table('persons', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
