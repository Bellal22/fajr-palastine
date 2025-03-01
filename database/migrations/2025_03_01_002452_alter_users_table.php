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
        Schema::table('users', function (Blueprint $table) {
            $table->string('type')->nullable();

            $table->string('phone')->unique()->nullable();

            $table->string('firebase_id')->unique()->nullable();

            $table->timestamp('phone_verified_at')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(
                'type',
                'phone',
                'firebase_id',
                'phone_verified_at',
            );
        });
    }
};
