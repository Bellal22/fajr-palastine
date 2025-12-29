<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_contents', function (Blueprint $table) {
            $table->id();

            // polymorphic relationship - يمكن أن يكون الطرد جاهز أو داخلي
            $table->morphs('package'); // creates package_id & package_type

            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1)->comment('كمية الصنف داخل الطرد');
            $table->timestamps();

            // منع التكرار
            $table->unique(['package_id', 'package_type', 'item_id'], 'package_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_contents');
    }
}