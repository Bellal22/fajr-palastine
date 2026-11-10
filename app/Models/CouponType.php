<?php

namespace App\Models;

use App\Http\Filters\Filterable;
use App\Support\Traits\Selectable;
use App\Http\Filters\CouponTypeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CouponType extends Model
{
    use HasFactory;
    use Filterable;
    use Selectable;

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
    protected $filter = CouponTypeFilter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_cash',
        'cash_amount_type',
        'cash_amount',
        'cash_currency',
    ];

    protected $casts = [
        'is_cash' => 'boolean',
        'cash_amount_type' => 'string',
        'cash_amount' => 'decimal:2',
    ];

    public static function ensureCashColumnsExist(): void
    {
        try {
            if (Schema::hasTable('coupon_types')) {
                if (!Schema::hasColumn('coupon_types', 'is_cash')) {
                    Schema::table('coupon_types', function (Blueprint $table) {
                        $table->boolean('is_cash')->default(false)->after('name');
                        $table->decimal('cash_amount', 12, 2)->nullable()->after('is_cash');
                        $table->string('cash_currency', 10)->nullable()->after('cash_amount');
                    });
                }
                if (!Schema::hasColumn('coupon_types', 'cash_amount_type')) {
                    Schema::table('coupon_types', function (Blueprint $table) {
                        $table->string('cash_amount_type', 20)->default('fixed')->after('is_cash');
                    });
                }
            }
        } catch (\Throwable $e) {
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

    public function getCurrencyNameAttribute(): string
    {
        return self::CURRENCY_NAMES[$this->clean_currency_code] ?? 'شيكل';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return self::CURRENCY_SYMBOLS[$this->clean_currency_code] ?? '₪';
    }

    public function getCurrencyLabelAttribute(): string
    {
        return self::CURRENCIES[$this->clean_currency_code] ?? 'شيكل (₪)';
    }

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
}
