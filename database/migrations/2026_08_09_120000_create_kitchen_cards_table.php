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
        if (!Schema::hasTable('kitchen_cards')) {
            Schema::create('kitchen_cards', function (Blueprint $table) {
                $table->id();
                $table->string('camp_name');
                $table->string('card_number');
                $table->string('barcode')->unique();
                $table->timestamps();

                $table->unique(['camp_name', 'card_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_cards');
    }
};
