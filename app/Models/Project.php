<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\ProjectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Project extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;
    use SoftDeletes;

    /**
     * العملات المتاحة للمشاريع والكوبونات النقدية
     */
    public const CURRENCIES = [
        'ILS' => 'شيكل (₪)',
        'USD' => 'دولار ($)',
        'JOD' => 'دينار (د.أ)',
        'EUR' => 'يورو (€)',
    ];

    public const CURRENCY_SYMBOLS = [
        'ILS' => '₪',
        'USD' => '$',
        'JOD' => 'د.أ',
        'EUR' => '€',
    ];

    public const CURRENCY_NAMES = [
        'ILS' => 'شيكل',
        'USD' => 'دولار',
        'JOD' => 'دينار',
        'EUR' => 'يورو',
    ];

    public const CASH_AMOUNT_TYPES = [
        'fixed' => 'موحد لجميع المستفيدين',
        'custom' => 'منفصل لكل مستفيد (يحدد عند التسليم)',
    ];

    public const DELIVERY_METHODS = [
        'كاش' => 'كاش (نقداً باليد)',
        'محفظة' => 'محفظة إلكترونية',
        'بنك' => 'حساب بنكي / تحويل',
    ];

    /**
     * The query parameter's filter of the model.
     *
     * @var string
     */
    protected $filter = ProjectFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'is_cash',
        'cash_amount_type',
        'cash_amount',
        'cash_currency',
        'default_delivery_method',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_cash' => 'boolean',
        'cash_amount_type' => 'string',
        'cash_amount' => 'decimal:2',
    ];

    /**
     * التأكد من وجود أعمدة الدعم النقدي في جدول projects تلقائياً
     */
    public static function ensureCashColumnsExist(): void
    {
        try {
            if (Schema::hasTable('projects')) {
                if (!Schema::hasColumn('projects', 'is_cash')) {
                    Schema::table('projects', function (Blueprint $table) {
                        $table->boolean('is_cash')->default(false)->after('status');
                        $table->decimal('cash_amount', 12, 2)->nullable()->after('is_cash');
                        $table->string('cash_currency', 10)->nullable()->after('cash_amount');
                    });
                }
                if (!Schema::hasColumn('projects', 'cash_amount_type')) {
                    Schema::table('projects', function (Blueprint $table) {
                        $table->string('cash_amount_type', 20)->default('fixed')->after('is_cash');
                    });
                }
                if (!Schema::hasColumn('projects', 'default_delivery_method')) {
                    Schema::table('projects', function (Blueprint $table) {
                        $table->string('default_delivery_method', 30)->nullable()->after('cash_currency');
                    });
                }
            }

            if (Schema::hasTable('project_beneficiaries')) {
                Schema::table('project_beneficiaries', function (Blueprint $table) {
                    if (!Schema::hasColumn('project_beneficiaries', 'delivery_method')) {
                        $table->string('delivery_method', 50)->nullable()->after('delivery_date');
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
        } catch (\Throwable $e) {
            // تجاهل الخطأ في حال عدم القدرة على تعديل الجدول مؤقتاً
        }
    }

    /**
     * كود العملة القياسي الموحد (ILS, USD, JOD, EUR)
     */
    public function getCleanCurrencyCodeAttribute(): string
    {
        $c = (string)$this->cash_currency;
        if (isset(self::CURRENCY_SYMBOLS[$c])) return $c;
        if (str_contains($c, 'شيكل') || str_contains($c, '₪')) return 'ILS';
        if (str_contains($c, 'دولار') || str_contains($c, '$')) return 'USD';
        if (str_contains($c, 'دينار') || str_contains($c, 'د.أ')) return 'JOD';
        if (str_contains($c, 'يورو') || str_contains($c, '€')) return 'EUR';
        return 'ILS';
    }

    /**
     * اسم العملة بالعربية
     */
    public function getCurrencyNameAttribute(): string
    {
        return self::CURRENCY_NAMES[$this->clean_currency_code] ?? 'شيكل';
    }

    /**
     * رمز العملة
     */
    public function getCurrencySymbolAttribute(): string
    {
        return self::CURRENCY_SYMBOLS[$this->clean_currency_code] ?? '₪';
    }

    /**
     * اسم العملة مع الرمز
     */
    public function getCurrencyLabelAttribute(): string
    {
        return self::CURRENCIES[$this->clean_currency_code] ?? 'شيكل (₪)';
    }

    /**
     * القيمة النقدية منسقة مع العملة (مثال: 100 ₪)
     */
    public function getFormattedCashValueAttribute(): string
    {
        if (!$this->is_cash) {
            return '';
        }
        if ($this->cash_amount_type === 'custom') {
            return 'مبلغ منفصل لكل مستفيد (' . $this->currency_symbol . ')';
        }
        if ($this->cash_amount === null) {
            return 'غير محدد (' . $this->currency_symbol . ')';
        }
        $amount = rtrim(rtrim(number_format((float) $this->cash_amount, 2, '.', ','), '0'), '.');
        return $amount . ' ' . $this->currency_symbol;
    }

    /**
     * Custom attributes storage.
     *
     * @var array
     */
    protected $customAttributes = [];

    /**
     * Magic getter override.
     */
    public function __get($key)
    {
        // Check custom attributes first
        if (array_key_exists($key, $this->customAttributes)) {
            return $this->customAttributes[$key];
        }

        return parent::__get($key);
    }

    /**
     * Magic setter override.
     */
    public function __set($key, $value)
    {
        // Store these as custom attributes
        if (in_array($key, ['area_breakdown', 'warehouse_breakdown'])) {
            $this->customAttributes[$key] = $value;
            return;
        }

        parent::__set($key, $value);
    }

    /**
     * Magic isset override.
     */
    public function __isset($key)
    {
        if (array_key_exists($key, $this->customAttributes)) {
            return isset($this->customAttributes[$key]);
        }

        return parent::__isset($key);
    }

    // ... باقي الكود كما هو ...

    /**
     * Check if project is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Check if project can accept beneficiaries.
     */
    public function canAcceptBeneficiaries(): bool
    {
        return !$this->isCompleted() && !$this->isExpired();
    }

    /**
     * Auto-update status if expired.
     */
    public function checkAndUpdateStatus()
    {
        if ($this->isExpired() && !$this->isCompleted()) {
            $this->update(['status' => 'completed']);
        }
    }

    /**
     * Get the executing entities (Suppliers).
     */
    public function executingEntities()
    {
        return $this->belongsToMany(Supplier::class, 'project_partners')
            ->wherePivot('type', 'executing')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Get the granting entities (Suppliers).
     */
    public function grantingEntities()
    {
        return $this->belongsToMany(Supplier::class, 'project_partners')
            ->wherePivot('type', 'granting')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Get all partners (both granting and executing).
     */
    public function partners()
    {
        return $this->belongsToMany(Supplier::class, 'project_partners')
            ->withPivot('type')
            ->withTimestamps();
    }

    /**
     * Get the coupon types associated with the project.
     */
    public function couponTypes()
    {
        return $this->belongsToMany(CouponType::class, 'project_coupon_types')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get the projects that conflict with this project.
     */
    public function conflicts()
    {
        return $this->belongsToMany(Project::class, 'project_conflicts', 'project_id', 'conflict_id')
            ->withTimestamps();
    }

    /**
     * Get the ready packages associated with the project.
     */
    public function readyPackages()
    {
        return $this->morphedByMany(ReadyPackage::class, 'packageable', 'project_packages')
            ->withTimestamps();
    }

    /**
     * Get the internal packages associated with the project.
     */
    public function internalPackages()
    {
        return $this->morphedByMany(InternalPackage::class, 'packageable', 'project_packages')
            ->withTimestamps();
    }

    /**
     * Get all packages (both ready and internal).
     */
    public function allPackages()
    {
        return $this->readyPackages->merge($this->internalPackages);
    }

    /**
     * Get the outbound shipments for the project.
     */
    public function outboundShipments()
    {
        return $this->hasMany(OutboundShipment::class);
    }

    /**
     * Get the inbound shipments for the project.
     */
    public function inboundShipments()
    {
        return $this->hasMany(InboundShipment::class);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if project is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if project is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function beneficiaries()
    {
        return $this->belongsToMany(Person::class, 'project_beneficiaries')
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id')
            ->withTimestamps();
    }

    public function incompleteBeneficiaries()
    {
        return $this->belongsToMany(IncompletePerson::class, 'project_beneficiaries', 'project_id', 'incomplete_person_id')
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id')
            ->withTimestamps();
    }

    public function employeeBeneficiaries()
    {
        return $this->belongsToMany(Employee::class, 'project_beneficiaries', 'project_id', 'employee_id')
            ->withPivot('status', 'notes', 'delivery_date', 'quantity', 'sub_warehouse_id')
            ->withTimestamps();
    }

    public function needRequestProject()
    {
        return $this->hasOne(NeedRequestProject::class)->latestOfMany();
    }

    public function needRequestProjects()
    {
        return $this->hasMany(NeedRequestProject::class);
    }

    public function latestNeedRequestProject()
    {
        return $this->hasOne(NeedRequestProject::class)->latest();
    }

    public function needRequests()
    {
        return $this->hasMany(NeedRequest::class);
    }
}
