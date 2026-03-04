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
        Schema::table('need_request_projects', function (Blueprint $blueprint) {
            $blueprint->integer('min_family_members')->nullable()->after('deadline');
            $blueprint->integer('max_family_members')->nullable()->after('min_family_members');
            $blueprint->string('gender')->nullable()->after('max_family_members')->comment('male, female, both');
            $blueprint->text('social_status')->nullable()->after('gender')->comment('json array of statuses');
            $blueprint->text('employment_status')->nullable()->after('social_status')->comment('json array of statuses');
            $blueprint->text('housing_type')->nullable()->after('employment_status')->comment('json array');
            $blueprint->boolean('has_condition')->nullable()->after('housing_type');
            $blueprint->integer('min_age')->nullable()->after('has_condition');
            $blueprint->integer('max_age')->nullable()->after('min_age');
            
            // New Detailed Criteria
            $blueprint->integer('child_min_age')->nullable()->after('max_age');
            $blueprint->integer('child_max_age')->nullable()->after('child_min_age');
            $blueprint->integer('child_count')->nullable()->after('child_max_age');
            $blueprint->boolean('has_disability')->nullable()->after('child_count');
            $blueprint->boolean('has_chronic_disease')->nullable()->after('has_disability');
            $blueprint->text('target_neighborhoods')->nullable()->after('has_chronic_disease')->comment('json array');
            
            $blueprint->text('criteria_notes')->nullable()->after('target_neighborhoods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('need_request_projects', function (Blueprint $blueprint) {
            $blueprint->dropColumn([
                'min_family_members',
                'max_family_members',
                'gender',
                'social_status',
                'employment_status',
                'housing_type',
                'has_condition',
                'min_age',
                'max_age',
                'criteria_notes'
            ]);
        });
    }
};
