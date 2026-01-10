<?php

namespace App\Http\Controllers;

use App\Models\BanList;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function getFamilyMemberData($id)
    {
        try {
            $familyMember = Person::find($id);

            if (!$familyMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على بيانات العضو المطلوب.'
                ], 404);
            }

            // ✅ التحقق من قائمة الحظر
            $banned = BanList::where('id_num', $familyMember->id_num)->first();
            if ($banned) {
                return response()->json([
                    'success' => false,
                    'rejected_id' => $banned->id_num,
                    'reason' => $banned->reason ?? 'غير محدد',
                    'message' => "لا يمكن تعديل هذا العضو لأنه محظور من النظام. السبب: " . ($banned->reason ?? 'غير محدد')
                ], 422);
            }

            // ✅ معالجة رقم الجوال: إضافة الصفر إذا كان محذوفاً
            $phone = $familyMember->phone;
            if ($phone && strlen($phone) === 9) {
                $phone = '0' . $phone;
            }

            // 🔍 تشخيص: طباعة رقم الجوال في الـ log
            Log::info("📱 Phone for member {$id}: " . ($phone ?? 'null'));

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $familyMember->id,
                    'first_name' => $familyMember->first_name,
                    'father_name' => $familyMember->father_name,
                    'grandfather_name' => $familyMember->grandfather_name,
                    'family_name' => $familyMember->family_name,
                    'id_num' => $familyMember->id_num,
                    'dob' => $familyMember->dob,
                    'relationship' => $familyMember->relationship,
                    'has_condition' => $familyMember->has_condition,
                    'condition_description' => $familyMember->condition_description,
                    'phone' => $phone, // ✅ إضافة رقم الجوال
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('❌ خطأ في getFamilyMemberData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات العضو.'
            ], 500);
        }
    }
}
