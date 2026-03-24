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
            $table->integer('remaining_quantity')->default(0)->after('quantity');
            $table->integer('min_stock')->default(5)->after('remaining_quantity');
        });

        Schema::table('ready_packages', function (Blueprint $table) {
            $table->integer('remaining_quantity')->default(0)->after('quantity');
            $table->integer('min_stock')->default(5)->after('remaining_quantity');
        });

        Schema::table('internal_packages', function (Blueprint $table) {
            $table->integer('remaining_quantity')->default(0)->after('quantity');
            $table->integer('min_stock')->default(5)->after('remaining_quantity');
        });

        // Initialize remaining_quantity for existing records
        \DB::table('items')->update(['remaining_quantity' => \DB::raw('quantity')]);
        \DB::table('ready_packages')->update(['remaining_quantity' => \DB::raw('quantity')]);
        \DB::table('internal_packages')->update(['remaining_quantity' => \DB::raw('quantity')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['remaining_quantity', 'min_stock']);
        });

        Schema::table('ready_packages', function (Blueprint $table) {
            $table->dropColumn(['remaining_quantity', 'min_stock']);
        });

        Schema::table('internal_packages', function (Blueprint $table) {
            $table->dropColumn(['remaining_quantity', 'min_stock']);
        });
    }
};
