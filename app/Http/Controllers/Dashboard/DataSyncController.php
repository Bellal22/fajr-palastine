<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Person;
use App\Models\Block;
use App\Models\AreaResponsible;

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
            'id_num'            => null,
            'passkey'           => null,
            'first_name'        => null,
            'father_name'       => null,
            'grandfather_name'  => null,
            'family_name'       => null,
            'phone'             => null,
            'relatives_count'   => 0,
            'block_id'          => null,
            'area_responsible_id' => null,
            'relative_id'       => null,
            'relationship'      => null,
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

                $representativeValue = trim($person['Representative_name'] ?? '');
                if (empty($representativeValue)) {
                    Log::warning("سجل الشخص رقم: {$person['CI_ID_NUM']} لا يحتوي على اسم أو رقم مندوب.");
                    $skippedCount++;
                    continue;
                }

                $block = null;

                // البحث عن المندوب بالرقم أو بالاسم
                if (is_numeric($representativeValue)) {
                    $block = Block::find($representativeValue);
                    if ($block) {
                        Log::info("تم العثور على المندوب '{$block->name}' بالرقم (ID: {$representativeValue}) للشخص رقم: {$person['CI_ID_NUM']}");
                    } else {
                        Log::warning("لم يتم العثور على مندوب بالرقم (ID: {$representativeValue}) للشخص رقم: {$person['CI_ID_NUM']}. سيتم تجاهل السجل.");
                    }
                } else {
                    $block = Block::where('name', 'LIKE', '%' . $representativeValue . '%')->first();
                    if ($block) {
                        Log::info("تم العثور على المندوب '{$block->name}' بالاسم المطابق لـ '{$representativeValue}' للشخص رقم: {$person['CI_ID_NUM']}");
                    } else {
                        Log::warning("لم يتم العثور على مندوب بالاسم المطابق لـ '{$representativeValue}' للشخص رقم: {$person['CI_ID_NUM']}. سيتم تجاهل السجل.");
                    }
                }

                // إذا تم العثور على المندوب، قم بإنشاء السجلات
                if ($block) {
                    // **التحقق الإضافي يبدأ من هنا**
                    // التحقق مما إذا كان المندوب مرتبطًا بمسؤول منطقة
                    if ($block->area_responsible_id) {
                        Log::info("المندوب '{$block->name}' مرتبط بمسؤول المنطقة رقم: {$block->area_responsible_id}.");
                    } else {
                        Log::warning("المندوب '{$block->name}' (ID: {$block->id}) غير مرتبط بأي مسؤول منطقة. سيتم تخزين الشخص مع قيمة null في حقل area_responsible_id.");
                    }
                    // **التحقق الإضافي ينتهي هنا**

                    // --- إنشاء سجل الزوج ---
                    $husbandRecord = $recordTemplate;
                    $husbandRecord['id_num'] = $person['CI_ID_NUM'];
                    $husbandRecord['passkey'] = '123456789';
                    $husbandRecord['first_name'] = $person['CI_FIRST_ARB'] ?? null;
                    $husbandRecord['father_name'] = $person['CI_FATHER_ARB'] ?? null;
                    $husbandRecord['grandfather_name'] = $person['CI_GRAND_FATHER_ARB'] ?? null;
                    $husbandRecord['family_name'] = $person['CI_FAMILY_ARB'] ?? null;
                    $husbandRecord['phone'] = $person['Phone_number'] ?? null;
                    $husbandRecord['relatives_count'] = $person['Family_count'] ?? 0;

                    // تخزين معرفات المندوب ومسؤول المنطقة
                    $husbandRecord['block_id'] = $block->id;
                    $husbandRecord['area_responsible_id'] = $block->area_responsible_id; // **هنا يتم تخزين معرف مسؤول المنطقة**

                    $beneficiariesToInsert[] = $husbandRecord;

                    // --- إنشاء سجل الزوجة ---
                    if (!empty($person['Wife_id']) && !empty($person['Wife_name'])) {
                        $wifeNameParts = $this->parseFullName($person['Wife_name']);
                        $wifeRecord = $recordTemplate;
                        $wifeRecord['id_num'] = $person['Wife_id'];
                        $wifeRecord['passkey'] = null;
                        $wifeRecord['first_name'] = $wifeNameParts['first_name'];
                        $wifeRecord['father_name'] = $wifeNameParts['father_name'];
                        $wifeRecord['grandfather_name'] = $wifeNameParts['grandfather_name'];
                        $wifeRecord['family_name'] = $wifeNameParts['family_name'];
                        $wifeRecord['relative_id'] = $person['CI_ID_NUM'];
                        $wifeRecord['relationship'] = 'wife';
                        $beneficiariesToInsert[] = $wifeRecord;
                    }
                } else {
                    $skippedCount++;
                    continue;
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
                Log::info("تم تجاهل {$skippedCount} سجل بسبب عدم العثور على مندوب مطابق أو بيانات ناقصة.");
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
     * دالة مساعدة لفصل الاسم الكامل إلى أربعة أجزاء.
     */
    private function parseFullName($fullName)
    {
        $parts = explode(' ', trim($fullName), 4);
        return [
            'first_name'        => $parts[0] ?? null,
            'father_name'       => $parts[1] ?? null,
            'grandfather_name'  => $parts[2] ?? null,
            'family_name'       => $parts[3] ?? null,
        ];
    }
}
