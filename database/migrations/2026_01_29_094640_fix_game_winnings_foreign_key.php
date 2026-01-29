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
        Schema::table('game_winnings', function (Blueprint $table) {
            // Drop old foreign key
            $table->dropForeign(['person_id']);
            
            // Re-add with correct table name
            $table->foreign('person_id')
                ->references('id')
                ->on('persons')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_winnings', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            
            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->nullOnDelete();
        });
    }
};
