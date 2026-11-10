<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CustomProject extends Model
{
    use HasFactory;

    protected $table = 'custom_projects';

    public const CURRENCIES = [
        'ILS' => 'شيكل (₪)',
        'USD' => 'دولار ($)',
        'JOD' => 'دينار (د.أ)',
        'EUR' => 'يورو (€)',
    ];

    public const CURRENCY_NAMES = [
        'ILS' => 'شيكل',
        'USD' => 'دولار',
        'JOD' => 'دينار',
        'EUR' => 'يورو',
    ];

    protected $fillable = [
        'name',
        'slug',
        'code_prefix',
        'icon',
        'color',
        'description',
        'is_cash',
        'cash_amount',
        'cash_currency',
        'coupon_scope',
        'default_project_ids',
        'default_project_quantities',
        'default_sub_warehouse_id',
        'auto_add_if_missing',
        'skip_already_delivered',
        'is_active',
    ];

    protected $casts = [
        'is_cash' => 'boolean',
        'cash_amount' => 'decimal:2',
        'default_project_ids' => 'array',
        'default_project_quantities' => 'array',
        'auto_add_if_missing' => 'boolean',
        'skip_already_delivered' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function setDefaultSubWarehouseIdAttribute($value)
    {
        $this->attributes['default_sub_warehouse_id'] = !empty($value) ? (int) $value : null;
    }

    public function getCurrencyNameAttribute(): string
    {
        return self::CURRENCY_NAMES[$this->cash_currency] ?? ($this->cash_currency ?: 'شيكل');
    }

    public function getCurrencyLabelAttribute(): string
    {
        return self::CURRENCIES[$this->cash_currency] ?? ($this->cash_currency ?: 'شيكل (₪)');
    }

    public function getFormattedCashValueAttribute(): string
    {
        if (!$this->is_cash || $this->cash_amount === null) {
            return '';
        }
        $amount = rtrim(rtrim(number_format((float) $this->cash_amount, 2, '.', ','), '0'), '.');
        return $amount . ' ' . $this->currency_name;
    }

    /**
     * Batches / delivery sheets belonging to this custom project.
     */
    public function batches()
    {
        return $this->hasMany(RrmBatch::class, 'custom_project_id');
    }

    /**
     * Default sub-warehouse relationship.
     */
    public function defaultSubWarehouse()
    {
        return $this->belongsTo(SubWarehouse::class, 'default_sub_warehouse_id');
    }

    /**
     * Get the default Project (coupon) models configured for this custom project.
     */
    public function getDefaultProjectsAttribute()
    {
        $ids = $this->default_project_ids ?? [];
        if (empty($ids)) {
            return collect();
        }
        return Project::whereIn('id', $ids)->get();
    }

    /**
     * Human-readable label for coupon_scope.
     */
    public function getCouponScopeLabelAttribute(): string
    {
        return match ($this->coupon_scope) {
            'single' => 'طرد واحد محدد',
            'multiple' => 'حزمة طرود متعددة',
            default => 'مرن (طرد واحد أو حزمة طرود)',
        };
    }

    /**
     * Get the permission name associated with this custom project.
     */
    public function getPermissionNameAttribute(): string
    {
        return $this->slug === 'rrm' ? 'manage.rrm' : ('manage.custom_project.' . $this->slug);
    }

    /**
     * Determine whether the given user can access this custom project.
     */
    public function userCanAccess($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isAdmin() || $user->hasPermission('manage.admins') || $user->hasPermission('manage.custom_projects')) {
            return true;
        }

        return $user->hasPermission($this->permission_name);
    }

    /**
     * Ensure table and columns exist, and seed the default RRM custom project if missing.
     */
    public static function ensureTableExists(): void
    {
        RrmBatch::ensureTableExists();
        Project::ensureCashColumnsExist();

        if (!Schema::hasTable('custom_projects')) {
            try {
                Schema::create('custom_projects', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->string('slug')->unique();
                    $table->string('code_prefix', 20)->default('RRM');
                    $table->string('icon', 60)->default('fas fa-first-aid');
                    $table->string('color', 30)->default('warning');
                    $table->text('description')->nullable();
                    $table->boolean('is_cash')->default(false);
                    $table->decimal('cash_amount', 12, 2)->nullable();
                    $table->string('cash_currency', 10)->nullable();
                    $table->string('coupon_scope', 20)->default('multiple'); // single, multiple, both
                    $table->text('default_project_ids')->nullable();
                    $table->text('default_project_quantities')->nullable();
                    $table->unsignedBigInteger('default_sub_warehouse_id')->nullable();
                    $table->boolean('auto_add_if_missing')->default(true);
                    $table->boolean('skip_already_delivered')->default(true);
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasTable('custom_projects') && !Schema::hasColumn('custom_projects', 'is_cash')) {
            try {
                Schema::table('custom_projects', function ($table) {
                    $table->boolean('is_cash')->default(false)->after('description');
                    $table->decimal('cash_amount', 12, 2)->nullable()->after('is_cash');
                    $table->string('cash_currency', 10)->nullable()->after('cash_amount');
                });
            } catch (\Throwable $e) {
            }
        }

        if (Schema::hasTable('rrm_batches') && !Schema::hasColumn('rrm_batches', 'custom_project_id')) {
            try {
                Schema::table('rrm_batches', function ($table) {
                    $table->unsignedBigInteger('custom_project_id')->nullable()->after('id');
                    $table->index('custom_project_id');
                });
            } catch (\Throwable $e) {
            }
        }

        // Seed the default RRM project if not present and link orphan batches to it
        try {
            if (Schema::hasTable('custom_projects')) {
                $rrm = static::where('slug', 'rrm')->first();
                if (!$rrm) {
                    $rrm = static::create([
                        'name' => 'مشروع الـ RRM',
                        'slug' => 'rrm',
                        'code_prefix' => 'RRM',
                        'icon' => 'fas fa-first-aid',
                        'color' => 'warning',
                        'description' => 'آلية الاستجابة السريعة (RRM) لإعداد وتوزيع كشوفات وحزم الطوارئ المستقلة.',
                        'coupon_scope' => 'multiple',
                        'default_project_ids' => [],
                        'default_project_quantities' => [],
                        'auto_add_if_missing' => true,
                        'skip_already_delivered' => true,
                        'is_active' => true,
                    ]);
                }

                if ($rrm && Schema::hasTable('rrm_batches') && Schema::hasColumn('rrm_batches', 'custom_project_id')) {
                    DB::table('rrm_batches')
                        ->whereNull('custom_project_id')
                        ->update(['custom_project_id' => $rrm->id]);
                }
            }
        } catch (\Throwable $e) {
        }
    }
}

