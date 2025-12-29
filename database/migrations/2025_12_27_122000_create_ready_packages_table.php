<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReadyPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ready_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_shipment_id')
                ->constrained('inbound_shipments')
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('weight')->nullable()->comment('الوزن بالكيلوجرام');
            $table->unsignedInteger('quantity')->default(1)->comment('عدد الطرود من هذا النوع');
            $table->softDeletes();
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
        Schema::dropIfExists('ready_packages');
    }
}
