<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectRelationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Project Coupon Types (List of Types + Quantity)
        Schema::create('project_coupon_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('coupon_type_id')->constrained('coupon_types')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // 2. Project Packages (List of Packages - Polymorphic)
        Schema::create('project_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->morphs('packageable'); // ready_package or internal_package
            // User didn't specify quantity for packages, but implies selection. 
            // We'll keep it simple: just the link.
            $table->timestamps();
        });

        // 3. Project Partners (Granting & Executing Agencies)
        Schema::create('project_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->enum('type', ['granting', 'executing']); // مانحة أو منفذة
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
        Schema::dropIfExists('project_partners');
        Schema::dropIfExists('project_packages');
        Schema::dropIfExists('project_coupon_types');
    }
}
