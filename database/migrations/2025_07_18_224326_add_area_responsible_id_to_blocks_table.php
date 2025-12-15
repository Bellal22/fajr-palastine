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
//        Schema::table('blocks', function (Blueprint $table) {
//            $table->foreignId('area_responsible_id')
//                ->nullable()
//                ->constrained('users','id')
//                ->nullOnDelete();
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropForeign(['area_responsible_id']);
            $table->dropColumn('area_responsible_id');
        });
    }
};
