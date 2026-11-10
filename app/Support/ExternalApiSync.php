<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalApiSync
{
    /**
     * Send Project data to external API.
     *
     * @param \App\Models\Project $project
     * @return void
     */
    public static function syncProject($project)
    {
        Log::info("محاولة مزامنة المشروع ID: {$project->id}");

        $apiUrl = config('services.external_api.url');
        if (!$apiUrl) {
            Log::warning('External API URL is not configured.');
            return;
        }

        // حساب إحصائيات المشروع (المستفيدين والتعارضات)
        $project->loadCount('beneficiaries as total_count');

        // حساب إجمالي الأسر (أرباب الأسر فقط)
        $totalFamilies = \App\Models\Person::where(function($q) {
            $q->whereNull('relative_id')->orWhere('relative_id', '')->orWhere('relative_id', 0);
        })->count();
        
        // حساب إجمالي الأفراد (الكل)
        $totalIndividuals = \App\Models\Person::count();
        
        // حساب إجمالي حالات التعارض العام (أشخاص مسجلين في أكثر من مشروع)
        $globalConflicts = \Illuminate\Support\Facades\DB::table('project_beneficiaries')
            ->select('person_id')
            ->groupBy('person_id')
            ->havingRaw('COUNT(project_id) > 1')
            ->get()
            ->count();

        $donors = $project->partners()->pluck('name')->toArray();

        // حساب التعارضات الخاصة بهذا المشروع فقط
        $internalConflicts = \Illuminate\Support\Facades\DB::table('project_beneficiaries')
            ->where('project_id', $project->id)
            ->whereIn('person_id', function($query) {
                $query->select('person_id')
                    ->from('project_beneficiaries')
                    ->groupBy('person_id')
                    ->havingRaw('COUNT(project_id) > 1');
            })->count();

        $data = [
            'type' => 'project',
            'id' => $project->id,
            'name_ar' => $project->name,
            'description_ar' => $project->description,
            'sector' => null,
            'status' => $project->status,
            'beneficiaries_count' => $project->total_count, 
            'registered_beneficiaries' => $project->total_count,
            'internal_conflicts' => $internalConflicts,
            'external_conflicts' => 0,
            'global_registered_individuals' => $totalIndividuals,
            'total_families_count' => $totalFamilies,
            'global_conflicts' => $globalConflicts,
            'start_date' => $project->start_date ? $project->start_date->format('d/m/Y') : null,
            'end_date' => $project->end_date ? $project->end_date->format('d/m/Y') : null,
            'donors' => implode(', ', $donors),
            'coupon_name' => $project->name,
        ];

        self::sendRequest($apiUrl, $data);
    }

    /**
     * Send Supplier data to external API.
     *
     * @param \App\Models\Supplier $supplier
     * @return void
     */
    public static function syncSupplier($supplier)
    {
        $apiUrl = config('services.external_api.url');
        if (!$apiUrl) {
            Log::warning('External API URL is not configured.');
            return;
        }

        $data = [
            'type' => 'supplier',
            'id' => $supplier->id,
            'name_ar' => $supplier->name,
            'description_ar' => $supplier->description,
            'supplier_type' => $supplier->type,
        ];

        self::sendRequest($apiUrl, $data);
    }

    /**
     * Send POST request to external API.
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    private static function sendRequest($url, $data)
    {
        try {
            $token = config('services.external_api.token');
            
            $response = Http::withHeaders([
                'X-API-TOKEN' => $token,
                'Accept' => 'application/json',
            ])->timeout(10)->post($url, $data);

            if ($response->successful()) {
                Log::info('External API Sync Success:', ['type' => $data['type'], 'id' => $data['id'] ?? 'global']);
            } else {
                Log::error('External API Sync Failed:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'data' => $data
                ]);
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('External API Sync Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync only global statistics to the external API.
     *
     * @return void
     */
    public static function syncGlobalStats()
    {
        $apiUrl = config('services.external_api.url');
        if (!$apiUrl) return;

        $totalFamilies = \App\Models\Person::where(function($q) {
            $q->whereNull('relative_id')->orWhere('relative_id', '')->orWhere('relative_id', 0);
        })->count();
        
        $totalIndividuals = \App\Models\Person::count();
        
        $globalConflicts = \Illuminate\Support\Facades\DB::table('project_beneficiaries')
            ->select('person_id')
            ->groupBy('person_id')
            ->havingRaw('COUNT(project_id) > 1')
            ->get()
            ->count();

        $data = [
            'type' => 'global_stats',
            'global_registered_individuals' => $totalIndividuals,
            'total_families_count' => $totalFamilies,
            'global_conflicts' => $globalConflicts,
        ];

        self::sendRequest($apiUrl, $data);
    }

    /**
     * Sync all projects and suppliers to the external API.
     *
     * @return array
     */
    public static function bulkSync()
    {
        \Illuminate\Support\Facades\Log::info('بدء عملية المزامنة الشاملة لجميع البيانات...');
        
        $projects = \App\Models\Project::all();
        $suppliers = \App\Models\Supplier::all();
        
        $successCount = 0;
        $errorCount = 0;

        foreach ($projects as $project) {
            try {
                self::syncProject($project);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Bulk Sync Project Error (ID: {$project->id}): " . $e->getMessage());
            }
        }

        foreach ($suppliers as $supplier) {
            try {
                self::syncSupplier($supplier);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Bulk Sync Supplier Error (ID: {$supplier->id}): " . $e->getMessage());
            }
        }

        return [
            'success' => $successCount,
            'errors' => $errorCount,
            'total' => $projects->count() + $suppliers->count()
        ];
    }
}
