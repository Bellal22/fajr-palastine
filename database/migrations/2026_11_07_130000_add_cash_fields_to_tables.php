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
        // 1. Coupon Types: نوع المبلغ (موحد أو منفصل لكل مستفيد)
        if (Schema::hasTable('coupon_types')) {
            Schema::table('coupon_types', function (Blueprint $table) {
                if (!Schema::hasColumn('coupon_types', 'cash_amount_type')) {
                    $table->string('cash_amount_type', 20)->default('fixed')->after('is_cash'); // fixed: موحد, custom: منفصل لكل مستفيد
                }
            });
        }

        // 2. Projects: نوع المبلغ وطريقة التسليم الافتراضية
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'cash_amount_type')) {
                    $table->string('cash_amount_type', 20)->default('fixed')->after('is_cash'); // fixed: موحد, custom: منفصل لكل مستفيد
                }
                if (!Schema::hasColumn('projects', 'default_delivery_method')) {
                    $table->string('default_delivery_method', 30)->nullable()->after('cash_currency'); // كاش, محفظة, بنك
                }
            });
        }

        // 3. Project Beneficiaries: طريقة التسليم والمبلغ المستلم والعملة وتفاصيل الاستلام
        if (Schema::hasTable('project_beneficiaries')) {
            Schema::table('project_beneficiaries', function (Blueprint $table) {
                if (!Schema::hasColumn('project_beneficiaries', 'delivery_method')) {
                    $table->string('delivery_method', 50)->nullable()->after('delivery_date'); // كاش, محفظة, بنك
                }
                if (!Schema::hasColumn('project_beneficiaries', 'cash_amount')) {
                    $table->decimal('cash_amount', 12, 2)->nullable()->after('delivery_method');
                }
                if (!Schema::hasColumn('project_beneficiaries', 'cash_currency')) {
                    $table->string('cash_currency', 10)->nullable()->after('cash_amount');
                }
                if (!Schema::hasColumn('project_beneficiaries', 'delivery_details')) {
                    $table->string('delivery_details', 255)->nullable()->after('cash_currency');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('coupon_types')) {
            Schema::table('coupon_types', function (Blueprint $table) {
                if (Schema::hasColumn('coupon_types', 'cash_amount_type')) {
                    $table->dropColumn('cash_amount_type');
                }
            });
        }

        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'cash_amount_type')) {
                    $table->dropColumn('cash_amount_type');
                }
                if (Schema::hasColumn('projects', 'default_delivery_method')) {
                    $table->dropColumn('default_delivery_method');
                }
            });
        }

        if (Schema::hasTable('project_beneficiaries')) {
            Schema::table('project_beneficiaries', function (Blueprint $table) {
                $cols = ['delivery_method', 'cash_amount', 'cash_currency', 'delivery_details'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('project_beneficiaries', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

