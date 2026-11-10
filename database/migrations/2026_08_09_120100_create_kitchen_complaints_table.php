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
        if (!Schema::hasTable('kitchen_complaints')) {
            Schema::create('kitchen_complaints', function (Blueprint $table) {
                $table->id();
                $table->string('camp_name');
                $table->string('card_number');
                $table->string('complainant_name');
                $table->string('mobile_number');
                $table->string('complaint_title');
                $table->text('complaint_text');
                $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending');
                $table->text('response')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->unsignedBigInteger('responded_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_complaints');
    }
};
