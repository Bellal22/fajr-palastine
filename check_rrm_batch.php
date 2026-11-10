<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RrmBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== RRM BATCHES BEFORE RECALCULATION ===\n";
$batches = RrmBatch::all();
foreach ($batches as $b) {
    echo "ID: {$b->id} | Code: {$b->code} | Name: {$b->name} | BeneficiariesCount: {$b->beneficiaries_count} | TotalParcels: {$b->total_parcels}\n";
}

echo "\n=== RUNNING RECALCULATE & HEAL ON ALL BATCHES ===\n";
foreach ($batches as $b) {
    $res = $b->recalculateStats();
    echo "Batch ID {$b->id} [{$b->name}]: Recalculated => Beneficiaries: {$res['beneficiaries_count']}, Parcels: {$res['total_parcels']}\n";
}

echo "\n=== RRM BATCHES AFTER RECALCULATION ===\n";
$batches = RrmBatch::all();
foreach ($batches as $b) {
    echo "ID: {$b->id} | Code: {$b->code} | Name: {$b->name} | BeneficiariesCount: {$b->beneficiaries_count} | TotalParcels: {$b->total_parcels}\n";
}

echo "\n=== VERIFYING LINKED ROWS IN PROJECT_BENEFICIARIES ===\n";
$hasRrmCol = Schema::hasColumn('project_beneficiaries', 'rrm_batch_id');
foreach ($batches as $b) {
    $queryCount = $b->getBeneficiaryRowsQuery()->count();
    $colCount = $hasRrmCol ? DB::table('project_beneficiaries')->where('rrm_batch_id', $b->id)->count() : 0;
    echo "Batch {$b->id} ({$b->name}): Rows via getBeneficiaryRowsQuery = {$queryCount} | Rows via rrm_batch_id col = {$colCount}\n";
}

echo "\n=== BATCH BENEFICIARIES DETAILS (QUAD NAME & TYPE QUANTITIES) ===\n";
$latestBatch = RrmBatch::latest()->first();
if ($latestBatch) {
    $projectsMap = \App\Models\Project::pluck('name', 'id')->toArray();
    $warehousesMap = \App\Models\SubWarehouse::pluck('name', 'id')->toArray();
    $controller = new \App\Http\Controllers\Dashboard\RrmController();
    $refMethod = new \ReflectionMethod($controller, 'resolveBatchBeneficiaries');
    $refMethod->setAccessible(true);
    $beneficiaries = $refMethod->invoke($controller, $latestBatch, $projectsMap, $warehousesMap, null);

    echo "Batch: {$latestBatch->name} (Code: {$latestBatch->code})\n";
    foreach ($beneficiaries as $b) {
        echo " - الهوية: {$b['id_num']} | الاسم رباعي: {$b['name']} | تاريخ التسليم: {$b['delivery_date']} | الجوال: {$b['phone']} | الطرود/الأنواع: {$b['parcels_summary']} | إجمالي الكمية: {$b['total_quantity']}\n";
    }
}

