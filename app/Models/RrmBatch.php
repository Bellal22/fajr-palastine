<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RrmBatch extends Model
{
    use HasFactory;

    protected $table = 'rrm_batches';

    protected $fillable = [
        'custom_project_id',
        'code',
        'name',
        'delivery_date',
        'sub_warehouse_id',
        'user_id',
        'project_ids',
        'project_quantities',
        'beneficiaries_count',
        'total_parcels',
        'notes',
    ];

    protected $casts = [
        'custom_project_id' => 'integer',
        'delivery_date' => 'date',
        'project_ids' => 'array',
        'project_quantities' => 'array',
        'beneficiaries_count' => 'integer',
        'total_parcels' => 'integer',
    ];

    /**
     * Relationship with CustomProject
     */
    public function customProject()
    {
        return $this->belongsTo(CustomProject::class, 'custom_project_id');
    }

    /**
     * Relationship with SubWarehouse
     */
    public function subWarehouse()
    {
        return $this->belongsTo(SubWarehouse::class, 'sub_warehouse_id');
    }

    /**
     * Relationship with User (creator / distributor)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get Project models for the projects in this batch.
     */
    public function getProjectsAttribute()
    {
        $ids = $this->project_ids ?? [];
        if (empty($ids)) {
            return collect();
        }
        return Project::whereIn('id', $ids)->get();
    }

    /**
     * Get raw beneficiary records query for this batch.
     */
    public function getBeneficiaryRowsQuery()
    {
        $query = DB::table('project_beneficiaries');
        $hasRrmCol = Schema::hasColumn('project_beneficiaries', 'rrm_batch_id');

        $query->where(function($q) use ($hasRrmCol) {
            if ($hasRrmCol) {
                $q->where('rrm_batch_id', $this->id);
            }
            if (!empty($this->code)) {
                $q->orWhere('notes', 'LIKE', "%[RRM:{$this->code}%")
                  ->orWhere('notes', 'LIKE', "%{$this->code}%");
            }
            if (!empty($this->name) && mb_strlen(trim($this->name)) >= 2) {
                $name = trim($this->name);
                $q->orWhere('notes', 'LIKE', "%[اسم_المجموعة:{$name}]%")
                  ->orWhere('notes', 'LIKE', "%|{$name}]%")
                  ->orWhere('notes', 'LIKE', "%{$name}%");
            }
        });

        // Constrain by project_ids if specified on the batch to ensure strict isolation
        if (!empty($this->project_ids) && is_array($this->project_ids)) {
            $query->whereIn('project_id', $this->project_ids);
        }

        return $query;
    }

    /**
     * Recalculate and update beneficiaries_count and total_parcels for this batch,
     * while auto-healing any unlinked or orphaned beneficiary rows.
     */
    public function recalculateStats()
    {
        $hasRrmCol = Schema::hasColumn('project_beneficiaries', 'rrm_batch_id');

        // 1. Auto-heal: Link any unlinked rows (rrm_batch_id IS NULL) that belong to this batch
        if ($hasRrmCol) {
            $healQuery = DB::table('project_beneficiaries')->whereNull('rrm_batch_id');

            $healQuery->where(function($q) {
                if (!empty($this->code)) {
                    $q->where('notes', 'LIKE', "%[RRM:{$this->code}%")
                      ->orWhere('notes', 'LIKE', "%{$this->code}%");
                }
                if (!empty($this->name) && mb_strlen(trim($this->name)) >= 2) {
                    $name = trim($this->name);
                    $method = !empty($this->code) ? 'orWhere' : 'where';
                    $q->$method('notes', 'LIKE', "%[اسم_المجموعة:{$name}]%")
                      ->orWhere('notes', 'LIKE', "%|{$name}]%")
                      ->orWhere('notes', 'LIKE', "%{$name}%");
                }
            });

            if (!empty($this->project_ids) && is_array($this->project_ids)) {
                $healQuery->whereIn('project_id', $this->project_ids);
            }

            try {
                $healQuery->update(['rrm_batch_id' => $this->id]);
            } catch (\Throwable $e) {}
        }

        // 2. Fetch all rows matching this batch
        $rows = $this->getBeneficiaryRowsQuery()->get();

        $uniqueBeneficiaries = [];
        $totalParcels = 0;

        $batchQuantities = $this->project_quantities ?? [];

        // Fetch coupon types quantities for all projects linked to this batch (الكمية من الأنواع)
        $projectIds = $rows->pluck('project_id')->filter()->unique()->toArray();
        $projectCouponTypes = collect();
        if (!empty($projectIds)) {
            $projectCouponTypes = DB::table('project_coupon_types')
                ->whereIn('project_id', $projectIds)
                ->select('project_id', DB::raw('SUM(quantity) as total_type_qty'))
                ->groupBy('project_id')
                ->pluck('total_type_qty', 'project_id');
        }

        foreach ($rows as $row) {
            $key = null;
            if (!empty($row->person_id)) {
                $key = 'p_' . $row->person_id;
            } elseif (!empty($row->employee_id)) {
                $key = 'e_' . $row->employee_id;
            } elseif (!empty($row->incomplete_person_id)) {
                $key = 'i_' . $row->incomplete_person_id;
            } else {
                $key = 'row_' . $row->id;
            }

            $uniqueBeneficiaries[$key] = true;

            $rowQty = max(1, (int)($row->quantity ?? 1));

            // Priority 1: Quantity from project coupon types (الأنواع)
            if (isset($projectCouponTypes[$row->project_id]) && (int)$projectCouponTypes[$row->project_id] > 0) {
                $typeQty = (int)$projectCouponTypes[$row->project_id];
                $finalQty = $typeQty * $rowQty;
            } elseif (!empty($batchQuantities) && isset($batchQuantities[$row->project_id]) && is_numeric($batchQuantities[$row->project_id])) {
                // Priority 2: Custom batch project_quantities
                $bQty = max(1, (int)$batchQuantities[$row->project_id]);
                $finalQty = ($bQty > 1) ? (($rowQty === 1) ? $bQty : ($rowQty * $bQty)) : $rowQty;
            } else {
                // Priority 3: Fallback parcel count
                $finalQty = $rowQty;
            }

            $totalParcels += $finalQty;
        }

        $beneficiariesCount = count($uniqueBeneficiaries);

        $this->beneficiaries_count = $beneficiariesCount;
        $this->total_parcels = $totalParcels;
        $this->save();

        return [
            'beneficiaries_count' => $beneficiariesCount,
            'total_parcels' => $totalParcels,
        ];
    }

    /**
     * Ensure the database table and columns exist safely.
     */
    public static function ensureTableExists()
    {
        if (!Schema::hasTable('rrm_batches')) {
            try {
                Schema::create('rrm_batches', function ($table) {
                    $table->id();
                    $table->string('code')->unique();
                    $table->string('name');
                    $table->date('delivery_date')->nullable();
                    $table->unsignedBigInteger('sub_warehouse_id')->nullable();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->text('project_ids')->nullable();
                    $table->text('project_quantities')->nullable();
                    $table->unsignedInteger('beneficiaries_count')->default(0);
                    $table->unsignedInteger('total_parcels')->default(0);
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('project_beneficiaries') && !Schema::hasColumn('project_beneficiaries', 'rrm_batch_id')) {
            try {
                Schema::table('project_beneficiaries', function ($table) {
                    $table->unsignedBigInteger('rrm_batch_id')->nullable()->after('notes');
                    $table->index('rrm_batch_id');
                });
            } catch (\Throwable $e) {}
        }
    }
}
