<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Person;
use App\Models\Block;
use App\Models\AreaResponsible;
use App\Models\BanList;

class DataSyncController extends Controller
{
    /**
     * استقبال البيانات من السيرفر الأول، تحويلها، وإضافتها لجدول Beneficiaries.
     */
    public function sync(Request $request)
    {
        // 1. التحقق من هوية السيرفر الأول
        $incomingApiKey = $request->header('X-API-KEY');
        $validApiKey = config('app.sync_api_key');

        if (!$incomingApiKey || $incomingApiKey !== $validApiKey) {
            Log::warning('محاولة مزامنة غير مصرح بها.', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sourceData = $request->input('beneficiary_data', []);

        if (empty($sourceData)) {
            return response()->json(['error' => 'No beneficiary data provided'], 400);
        }

        Log::info('تم استلام ' . count($sourceData) . ' سجل من السيرفر الأول');

        // 2. إنشاء قالب موحد لجميع السجلات
        $recordTemplate = [
            'id_num'              => null,
            'passkey'             => null,
            'first_name'          => null,
            'father_name'         => null,
            'grandfather_name'    => null,
            'family_name'         => null,
            'phone'               => null,
            'relatives_count'     => 2,
            'block_id'            => null,
            'area_responsible_id' => null,
            'relative_id'         => null,
            'relationship'        => null,
            'social_status'       => null,
        ];

        $beneficiariesToInsert = [];
        $skippedCount = 0;

        foreach ($sourceData as $index => $person) {
            try {
                if (!isset($person['CI_ID_NUM'])) {
                    Log::warning("سجل بدون CI_ID_NUM في الفهرس: {$index}");
                    $skippedCount++;
                    continue;
                }

                $personIdNum = trim($person['CI_ID_NUM']);

                // تحقق رقم الهوية للزوج في BanList أو Person
                if (BanList::where('id_num', $personIdNum)->exists() || Person::where('id_num', $personIdNum)->exists()) {
                    Log::warning("رقم الهوية {$personIdNum} للزوج محظور أو مسجل مسبقاً، تم تجاهل السجل في الفهرس: {$index}");
                    $skippedCount++;
                    continue;
                }

                $representativeValue = trim($person['Representative_name'] ?? '');
                if (empty($representativeValue)) {
                    Log::warning("سجل الشخص رقم: {$personIdNum} لا يحتوي على اسم أو رقم مندوب.");
                    $skippedCount++;
                    continue;
                }

                $block = null;

                if (is_numeric($representativeValue)) {
                    $block = Block::find($representativeValue);
                    if ($block) {
                        Log::info("تم العثور على المندوب '{$block->name}' بالرقم (ID: {$representativeValue}) للشخص رقم: {$personIdNum}");
                    } else {
                        Log::warning("لم يتم العثور على مندوب بالرقم (ID: {$representativeValue}) للشخص رقم: {$personIdNum}. سيتم تجاهل السجل.");
                    }
                } else {
                    $block = Block::where('name', 'LIKE', '%' . $representativeValue . '%')->first();
                    if ($block) {
                        Log::info("تم العثور على المندوب '{$block->name}' بالاسم المطابق لـ '{$representativeValue}' للشخص رقم: {$personIdNum}");
                    } else {
                        Log::warning("لم يتم العثور على مندوب بالاسم المطابق لـ '{$representativeValue}' للشخص رقم: {$personIdNum}. سيتم تجاهل السجل.");
                    }
                }

                if (!$block) {
                    $skippedCount++;
                    continue;
                }

                if ($block->area_responsible_id) {
                    Log::info("المندوب '{$block->name}' مرتبط بمسؤول المنطقة رقم: {$block->area_responsible_id}.");
                } else {
                    Log::warning("المندوب '{$block->name}' (ID: {$block->id}) غير مرتبط بأي مسؤول منطقة. سيتم تخزين الشخص مع قيمة null في حقل area_responsible_id.");
                }

                // تحقق رقم هوية الزوجة قبل إضافة السجل
                $wifeIdNum = trim($person['Wife_id'] ?? '');

                if (!empty($wifeIdNum) && (BanList::where('id_num', $wifeIdNum)->exists() || Person::where('id_num', $wifeIdNum)->exists())) {
                    Log::warning("رقم هوية الزوجة {$wifeIdNum} محظور أو مسجل مسبقاً، تم تجاهل السجل للزوج رقم: {$personIdNum} في الفهرس: {$index}");
                    $skippedCount++;
                    continue;
                }

                // استخراج الحالة الاجتماعية من عمود Notes
                $socialStatus = $this->extractSocialStatusFromNotes($person['Notes'] ?? '');

                // إنشاء سجل الزوج مع إضافة خانة الحالة الاجتماعية
                $husbandRecord = $recordTemplate;
                $husbandRecord['id_num'] = $personIdNum;
                $husbandRecord['passkey'] = '123456789';
                $husbandRecord['first_name'] = $person['CI_FIRST_ARB'] ?? null;
                $husbandRecord['father_name'] = $person['CI_FATHER_ARB'] ?? null;
                $husbandRecord['grandfather_name'] = $person['CI_GRAND_FATHER_ARB'] ?? null;
                $husbandRecord['family_name'] = $person['CI_FAMILY_ARB'] ?? null;
                $husbandRecord['phone'] = $person['Phone_number'] ?? null;
                $husbandRecord['relatives_count'] = $person['Family_count'] ?? 0;
                $husbandRecord['social_status'] = $socialStatus;
                $husbandRecord['block_id'] = $block->id;
                $husbandRecord['area_responsible_id'] = $block->area_responsible_id;

                $beneficiariesToInsert[] = $husbandRecord;

                // إنشاء سجل الزوجة
                if (!empty($person['Wife_id']) && !empty($person['Wife_name'])) {
                    $wifeNameParts = $this->parseFullName($person['Wife_name']);
                    $wifeRecord = $recordTemplate;
                    $wifeRecord['id_num'] = $person['Wife_id'];
                    $wifeRecord['passkey'] = null;
                    $wifeRecord['first_name'] = $wifeNameParts['first_name'];
                    $wifeRecord['father_name'] = $wifeNameParts['father_name'];
                    $wifeRecord['grandfather_name'] = $wifeNameParts['grandfather_name'];
                    $wifeRecord['family_name'] = $wifeNameParts['family_name'];
                    $wifeRecord['relative_id'] = $personIdNum;
                    $wifeRecord['relationship'] = 'wife';
                    $beneficiariesToInsert[] = $wifeRecord;
                }
            } catch (\Exception $e) {
                Log::error("خطأ في معالجة السجل في الفهرس {$index}: " . $e->getMessage());
                $skippedCount++;
                continue;
            }
        }

        // 3. إدخال البيانات
        DB::beginTransaction();
        try {
            if (!empty($beneficiariesToInsert)) {
                Person::upsert($beneficiariesToInsert, ['id_num']);
                Log::info('تمت مزامنة وإضافة ' . count($beneficiariesToInsert) . ' سجل بنجاح.');
            }

            if ($skippedCount > 0) {
                Log::info("تم تجاهل {$skippedCount} سجل بسبب عدم العثور على مندوب مطابق أو بيانات ناقصة أو وجودها مسبقاً.");
            }

            DB::commit();
            return response()->json([
                'message' => 'Person data synced successfully!',
                'inserted_count' => count($beneficiariesToInsert),
                'skipped_count' => $skippedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ أثناء مزامنة بيانات المستفيدين: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to sync Person data.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * دالة مساعدة لاستخراج الحالة الاجتماعية من النص
     */
    private function extractSocialStatusFromNotes($notes)
    {
        // البحث عن النمط "سبب الرفض: ... widowed|divorced|single"
        if (preg_match('/سبب الرفض:.*?(widowed|divorced|single)/u', $notes, $matches)) {
            return $matches[1];
        }

        // البحث عن الكلمات العربية مباشرة
        if (stripos($notes, 'أرمل/ة') !== false) {
            return 'widowed';
        } elseif (stripos($notes, 'مطلق/ة') !== false) {
            return 'divorced';
        } elseif (stripos($notes, 'انسة') !== false) {
            return 'single';
        }

        return null;
    }

    /**
     * دالة مساعدة لفصل الاسم الكامل إلى أربعة أجزاء.
     * تم تعديلها لإرجاع سلاسل فارغة بدلاً من null لتجنب أخطاء قاعدة البيانات.
     */
    private function parseFullName($fullName)
    {
        $parts = explode(' ', trim($fullName), 4);
        return [
            'first_name'        => $parts[0] ?? '',
            'father_name'       => $parts[1] ?? '',
            'grandfather_name'  => $parts[2] ?? '',
            'family_name'       => $parts[3] ?? '',
        ];
    }
}