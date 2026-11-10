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
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'response_title')) {
                $table->string('response_title')->nullable()->after('complaint_text');
            }
            if (!Schema::hasColumn('complaints', 'admin_complaint_title')) {
                $table->string('admin_complaint_title')->nullable()->after('response_title');
            }
            if (!Schema::hasColumn('complaints', 'admin_complaint_text')) {
                $table->text('admin_complaint_text')->nullable()->after('admin_complaint_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('complaints', 'response_title')) {
                $columns[] = 'response_title';
            }
            if (Schema::hasColumn('complaints', 'admin_complaint_title')) {
                $columns[] = 'admin_complaint_title';
            }
            if (Schema::hasColumn('complaints', 'admin_complaint_text')) {
                $columns[] = 'admin_complaint_text';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
