<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->integer('id_num')->nullable();
            $table->string('first_name');
            $table->string('father_name');
            $table->string('grandfather_name');
            $table->string('family_name');
            $table->date('dob')->nullable();
            $table->string('social_status')->nullable();
            $table->string('city')->nullable();
            $table->string('current_city')->nullable();
            $table->string('neighborhood')->nullable();
            $table->longText('landmark')->nullable();
            $table->string('housing_type')->nullable();
            $table->string('housing_damage_status')->nullable();
            $table->boolean('has_condition')->default(false);
            $table->longText('condition_description')->nullable();

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
        Schema::dropIfExists('families');
    }
}
