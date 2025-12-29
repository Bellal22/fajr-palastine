<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutboundShipmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outbound_shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outbound_shipment_id')
                ->constrained('outbound_shipments')
                ->cascadeOnDelete();

            // polymorphic - يمكن أن يكون طرد جاهز أو داخلي
            $table->morphs('shippable'); // creates shippable_id & shippable_type

            $table->unsignedInteger('quantity')->default(1);
            $table->float('weight')->nullable();
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
        Schema::dropIfExists('outbound_shipment_items');
    }
}
