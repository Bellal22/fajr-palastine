<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->enum('type', ['donor', 'operator'])
                ->default('donor')
                ->after('description')
                ->comment('donor = جهة مانحة, operator = جهة مشغلة');
            $table->string('image')->nullable()->after('type');
            $table->string('document')->nullable()->after('image');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['type', 'image', 'document']);
        });
    }
};
