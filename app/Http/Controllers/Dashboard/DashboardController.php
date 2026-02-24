<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Person;

class DashboardController extends Controller
{
    public function index()
    {
        $isRegionalManager = auth()->user()->isSupervisor();

        if ($isRegionalManager) {
            return view('dashboard.home', compact('isRegionalManager'));
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
            'disabilityPatientsStats'
        ));
    }
}