<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('area_responsible_id')->nullable()->constrained('area_responsibles')->onDelete('set null');
            $table->string('title', 255)->unique();
            $table->integer('phone');
            $table->integer('limit_num');
            $table->string('lan', 200);
            $table->string('lat', 200);
            $table->text('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blocks');
    }
}