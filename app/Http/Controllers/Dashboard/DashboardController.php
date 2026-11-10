<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\KitchenCard;
use App\Models\KitchenComplaint;
use App\Models\Person;
use App\Models\Item;
use App\Models\ReadyPackage;
use App\Models\InternalPackage;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function statistics()
    {
        if (!auth()->user()->hasPermission('manage.statistics')) {
            abort(403, 'غير مصرح لك بعرض لوحة الإحصائيات.');
        }
        $comprehensiveStats = Person::getComprehensiveDashboardStats();
        return view('dashboard.statistics.index', compact('comprehensiveStats'));
    }

    public function familyBreakdown(\Illuminate\Http\Request $request)
    {
        $type = $request->input('type', 'families');
        $search = trim($request->input('search', ''));
        $status = $request->input('status', 'all');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = max(5, min(100, (int) $request->input('per_page', 20)));

        $allFamilies = Person::getFamilyBreakdownStats($type);

        if (!empty($search)) {
            $normalizedSearch = self::normalizeArabicText($search);
            $allFamilies = array_filter($allFamilies, function ($item) use ($search, $normalizedSearch) {
                return (mb_strpos($item['display_name'], $search) !== false) 
                    || (mb_strpos($item['norm_name'], $normalizedSearch) !== false);
            });
        }

        if ($status === 'approved') {
            $allFamilies = array_filter($allFamilies, function ($item) {
                return $item['approved_count'] > 0;
            });
        } elseif ($status === 'unapproved') {
            $allFamilies = array_filter($allFamilies, function ($item) {
                return $item['unapproved_count'] > 0;
            });
        }

        $allFamilies = array_values($allFamilies);
        $totalItems = count($allFamilies);

        // Sum overall totals for current filtered category
        $sumTotal = array_sum(array_column($allFamilies, 'total_count'));
        $sumApproved = array_sum(array_column($allFamilies, 'approved_count'));
        $sumUnapproved = array_sum(array_column($allFamilies, 'unapproved_count'));

        $offset = ($page - 1) * $perPage;
        $pagedData = array_slice($allFamilies, $offset, $perPage);

        return response()->json([
            'success' => true,
            'data' => $pagedData,
            'summary' => [
                'total_count' => $sumTotal,
                'approved_count' => $sumApproved,
                'unapproved_count' => $sumUnapproved,
                'families_count' => $totalItems,
            ],
            'pagination' => [
                'total' => $totalItems,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($totalItems / $perPage)),
            ]
        ]);
    }

    public static function normalizeArabicText($text)
    {
        $text = trim($text);
        $text = str_replace(['أ', 'إ', 'آ', 'ء', 'ئ', 'ؤ'], 'ا', $text);
        $text = str_replace('ة', 'ه', $text);
        $text = str_replace('ى', 'ي', $text);
        $text = str_replace(['ـ', ' '], '', $text);
        return $text;
    }

    public function exportFamilyBreakdown(\Illuminate\Http\Request $request)
    {
        $type = $request->input('type', 'families');
        $search = trim($request->input('search', ''));
        $status = $request->input('status', 'all');

        $titles = [
            'families' => 'توزيع_الأسر_حسب_العائلة',
            'individuals' => 'توزيع_الأفراد_حسب_العائلة',
            'males' => 'توزيع_الذكور_حسب_العائلة',
            'females' => 'توزيع_الإناث_حسب_العائلة',
            'children' => 'توزيع_الأطفال_حسب_العائلة',
        ];

        $title = $titles[$type] ?? 'توزيع_العائلات';
        $filename = $title . '_' . date('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\FamilyBreakdownExport($type, $search, $status),
            $filename
        );
    }

    public function index()
    {
        if (auth()->user()->isGeneralSearchOnly()) {
            return redirect()->route('dashboard.people.general_search');
        }

        if (auth()->user()->isKitchenMonitor()) {
            $campsCount = DB::table('kitchen_cards')->distinct('camp_name')->count();
            $complaintsCount = KitchenComplaint::count();
            $isKitchenMonitor = true;

            return view('dashboard.home', compact('campsCount', 'complaintsCount', 'isKitchenMonitor'));
        }

        $isRegionalManager = auth()->user()->isSupervisor();
        $hasStatisticsPermission = auth()->user()->hasPermission('manage.statistics');

        if ($isRegionalManager || !$hasStatisticsPermission) {
            return view('dashboard.home', [
                'isRegionalManager' => $isRegionalManager,
                'hasStatisticsPermission' => $hasStatisticsPermission,
            ]);
        }

        // إحصائيات رب الأسرة
        $familyHeadStats = Person::getFamilyHeadStats();
        // ... (rest of the stats)
        $unapprovedCount = $familyHeadStats->unapproved_count;
        $approvedCount = $familyHeadStats->approved_count;
        $totalCount = $familyHeadStats->total_count;

        // إحصائيات التسجيل والمزامنة الشهرية
        $monthlyStats = Person::getMonthlyRegistrationStats();
        $registeredData = $monthlyStats['registered'];
        $syncedData = $monthlyStats['synced'];

        // إحصائيات الحالة الاجتماعية
        $socialStats = Person::getSocialStatusStats();
        $socialStatuses = [
            'أعزب' => 'single',
            'متزوج' => 'married',
            'متعدد' => 'polygamous',
            'مُطلق' => 'divorced',
            'أرمل' => 'widowed'
        ];

        $approvedSocialData = [];
        $unapprovedSocialData = [];
        foreach ($socialStatuses as $label => $value) {
            $stat = $socialStats->firstWhere('social_status', $value);
            $approvedSocialData[$label] = $stat->approved_count ?? 0;
            $unapprovedSocialData[$label] = $stat->unapproved_count ?? 0;
        }

        // إحصائيات الجندر
        $genderStats = Person::getGenderStats();
        $genderLabelsApproved = ['ذكر', 'أنثى', 'غير محدد'];
        $genderDataApproved = [
            $genderStats['approved']['ذكر'] ?? 0,
            $genderStats['approved']['أنثى'] ?? 0,
            $genderStats['approved']['غير محدد'] ?? 0
        ];

        $genderLabelsUnapproved = ['ذكر', 'أنثى', 'غير محدد'];
        $genderDataUnapproved = [
            $genderStats['unapproved']['ذكر'] ?? 0,
            $genderStats['unapproved']['أنثى'] ?? 0,
            $genderStats['unapproved']['غير محدد'] ?? 0
        ];

        // إحصائيات المناطق
        $areaStats = Person::getAreaStats();

        // إحصائيات الأطفال
        $childrenStats = Person::getChildrenStats();
        $childrenUnder1Count = $childrenStats->under_1;
        $childrenUnder3Count = $childrenStats->under_3;

        // ⭐ الإحصائيات الطبية كلها من استعلام واحد (محسّن)
        $medicalStats = Person::getAllMedicalStats();

        // تجهيز البيانات للـ View
        $pregnantNursingStats = [
            'pregnant_approved' => $medicalStats->pregnant_approved,
            'pregnant_unapproved' => $medicalStats->pregnant_unapproved,
            'nursing_approved' => $medicalStats->nursing_approved,
            'nursing_unapproved' => $medicalStats->nursing_unapproved,
        ];

        $cancerPatientsStats = [
            'cancer_approved' => $medicalStats->cancer_approved,
            'cancer_unapproved' => $medicalStats->cancer_unapproved,
        ];

        $injuredPatientsStats = [
            'injured_approved' => $medicalStats->injured_approved,
            'injured_unapproved' => $medicalStats->injured_unapproved,
        ];

        $kidneyPatientsStats = [
            'kidney_approved' => $medicalStats->kidney_approved,
            'kidney_unapproved' => $medicalStats->kidney_unapproved,
        ];

        $incontinenceStats = [
            'incontinence_approved' => $medicalStats->incontinence_approved,
            'incontinence_unapproved' => $medicalStats->incontinence_unapproved,
        ];

        $disabilityPatientsStats = [
            'disability_approved' => $medicalStats->disability_approved,
            'disability_unapproved' => $medicalStats->disability_unapproved,
        ];

        // تنبيهات نقص المخزون
        $lowStockItems = Item::lowStock()->get();
        $lowStockReadyPackages = ReadyPackage::lowStock()->get();
        $lowStockInternalPackages = InternalPackage::lowStock()->get();
        
        $hasLowStock = $lowStockItems->isNotEmpty() || $lowStockReadyPackages->isNotEmpty() || $lowStockInternalPackages->isNotEmpty();

        return view('dashboard.home', compact(
            'isRegionalManager',
            'unapprovedCount',
            'approvedCount',
            'totalCount',
            'registeredData',
            'syncedData',
            'areaStats',
            'approvedSocialData',
            'unapprovedSocialData',
            'childrenUnder1Count',
            'childrenUnder3Count',
            'genderLabelsApproved',
            'genderDataApproved',
            'genderLabelsUnapproved',
            'genderDataUnapproved',
            'pregnantNursingStats',
            'cancerPatientsStats',
            'injuredPatientsStats',
            'kidneyPatientsStats',
            'incontinenceStats',
            'disabilityPatientsStats',
            'lowStockItems',
            'lowStockReadyPackages',
            'lowStockInternalPackages',
            'hasLowStock'
        ));
    }
}