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
        Schema::dropIfExists('inbound_shipment_item');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('inbound_shipment_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_shipment_id')->constrained('inbound_shipments')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['inbound_shipment_id', 'item_id'], 'inbound_shipment_item_unique');
        });
    }
};