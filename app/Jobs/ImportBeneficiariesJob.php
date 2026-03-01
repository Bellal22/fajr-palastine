<?php

namespace App\Jobs;

use App\Models\Person;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportBeneficiariesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $filePath;
    protected $projectId;
    protected $requestData;

    public function __construct($filePath, $projectId, $requestData)
    {
        $this->filePath = $filePath;
        $this->projectId = $projectId;
        $this->requestData = $requestData;
    }

    public function handle()
    {
        set_time_limit(0);

        echo "\n--- STARTING JOB ---\n";

        $project = Project::find($this->projectId);
        if (!$project) return;

        $subWarehouseId = $this->requestData['sub_warehouse_id'];
        $ignoreConflicts = $this->requestData['ignore_conflicts'] ?? false;
        $overrideDeliveryDate = $this->requestData['delivery_date'] ?? null;
        $conflictingProjectIds = $project->conflicts()->pluck('conflict_id')->toArray();

        $imported = 0;
        $csvPath = null;

        try {
            $extension = strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION));
            $csvPath = $this->filePath;

            // 1. تحويل إلى CSV
            if (in_array($extension, ['xlsx', 'xls'])) {
                echo "Converting Excel to CSV...\n";
                $tempDir = storage_path('app/imports/temp');
                if (!is_dir($tempDir)) mkdir($tempDir, 0775, true);

                $reader = IOFactory::createReaderForFile($this->filePath);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($this->filePath);

                $csvPath = $tempDir . '/' . uniqid() . '.csv';

                $writer = IOFactory::createWriter($spreadsheet, 'Csv');
                $writer->setDelimiter(';');
                $writer->save($csvPath);

                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet, $reader, $writer);
                echo "Conversion done.\n";
            }

            if (!file_exists($csvPath)) return;

            $handle = fopen($csvPath, 'r');
            if ($handle === false) return;

            // 2. إعدادات القراءة
            $line1 = fgets($handle);
            rewind($handle);

            $delimiter = (strpos($line1, ';') === false && strpos($line1, ',') !== false) ? ',' : ';';

            // 3. قراءة العناوين
            $header = fgetcsv($handle, 0, $delimiter);

            $idIdx = 0;
            $qtyIdx = 3;
            $statusIdx = 5;
            $dateIdx = 7;
            $generalDateIdx = 4;
            $notesIdx = 6;

            if ($header) {
                foreach ($header as $i => $col) {
                    $col = trim($col);
                    if (mb_strpos($col, 'هوية') !== false) $idIdx = $i;
                    elseif (mb_strpos($col, 'كمية') !== false) $qtyIdx = $i;
                    elseif (mb_strpos($col, 'حالة') !== false || mb_strpos($col, 'استلام') !== false) $statusIdx = $i;
                    elseif (mb_strpos($col, 'ملاحظات') !== false) $notesIdx = $i;
                    elseif (mb_strpos($col, 'تاريخ') !== false) {
                        if (mb_strpos($col, 'تسليم') !== false) $dateIdx = $i;
                        else $generalDateIdx = $i;
                    }
                }
            }

            // 4. تحضير التعارضات
            $conflictPersonIds = [];
            if (!$ignoreConflicts && !empty($conflictingProjectIds)) {
                $conflictPersonIds = DB::table('project_beneficiaries')
                    ->whereIn('project_id', $conflictingProjectIds)
                    ->pluck('person_id')
                    ->toArray();
                $conflictPersonIds = array_flip($conflictPersonIds);
            }

            echo "Loading people data into memory for fast access...\n";

            // 5. تحميل كل الأشخاص مرة واحدة (لتجنب استعلامات داخل الحلقة)
            // نستخدمpluck('id', 'id_num') لإنشاء مصفوفة [id_num => id]
            $personsMap = DB::table('persons')->pluck('id', 'id_num')->toArray();
            echo "Loaded " . count($personsMap) . " persons.\n";

            // تحميل بيانات رب الأسرة (إذا كانت العلاقة تعتمد على عمود في جدول persons مثل 'head_id')
            // إذا كان لديك عمود head_id في جدول persons:
            // $headsMap = DB::table('persons')->pluck('head_id', 'id')->toArray();

            // للتوضيح: سنفترض الآن أن الشخص هو نفسه رب الأسرة لتجنب الـ Segfault
            // إذا كنت تريد منطق الربط، يجب أن يكون بسيطاً جداً هنا.

            $batchData = [];
            $rowNum = 0;

            // 6. حلقة المعالجة (Optimized)
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNum++;

                $idNumber = trim($row[$idIdx] ?? '');

                if (empty($idNumber)) continue;

                if ($rowNum % 5000 == 0) echo "Processing row: $rowNum\n";

                $paddedId = str_pad($idNumber, 9, '0', STR_PAD_LEFT);

                // البحث في المصفوفة المحملة (سريع جداً)
                if (!isset($personsMap[$paddedId])) continue;

                // الحصول على ID الشخص
                $personId = $personsMap[$paddedId];

                // منطق رب الأسرة: مؤقتاً نعتبر الشخص نفسه رب الأسرة
                // إذا كان لديك منطق الربط (Head)، يمكنك إضافة استعلام بسيط هنا
                // ولكن لتجنب الـ Segfault، لا نستدعي دالة Model معقدة هنا.
                $headId = $personId;

                // التحقق من التعارضات (من المصفوفة المحملة سابقاً)
                if (!$ignoreConflicts && isset($conflictPersonIds[$headId])) {
                    continue;
                }

                // البيانات
                $qty = is_numeric($row[$qtyIdx] ?? null) ? (int)$row[$qtyIdx] : 1;
                $statVal = trim($row[$statusIdx] ?? '');
                $notesVal = ($row[$notesIdx] ?? '') == '-' ? '' : trim($row[$notesIdx] ?? '');
                $dateVal = $this->resolveDate($row, $dateIdx, $generalDateIdx, $overrideDeliveryDate);

                $status = (mb_strpos($statVal, 'مستلم') !== false && mb_strpos($statVal, 'غير') === false) ? 'مستلم' : 'غير مستلم';

                $batchData[] = [
                    'project_id' => $project->id,
                    'person_id' => $headId,
                    'quantity' => $qty,
                    'status' => $status,
                    'notes' => $notesVal,
                    'delivery_date' => $dateVal,
                    'sub_warehouse_id' => $subWarehouseId
                ];

                // إدخال كل 500 سطر
                if (count($batchData) >= 500) {
                    $this->insertBatch($batchData);
                    $imported += count($batchData);
                    $batchData = [];
                }
            }

            // إدخال المتبقي
            if (!empty($batchData)) {
                $this->insertBatch($batchData);
                $imported += count($batchData);
            }

            fclose($handle);

            echo "--- JOB FINISHED ---\n";
            echo "Total Imported: $imported\n";

            if ($csvPath !== $this->filePath) @unlink($csvPath);
            @unlink($this->filePath);
        } catch (\Exception $e) {
            echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
        }
    }

    private function insertBatch($data)
    {
        // طريقة محسنة للإدخال (Upsert)
        // تتطلب أن يكون لديك فهرس فريد على (project_id, person_id)
        // إذا لم يكن موجوداً، استخدم الحلقة العادية
        foreach ($data as $item) {
            DB::table('project_beneficiaries')->updateOrInsert(
                ['project_id' => $item['project_id'], 'person_id' => $item['person_id']],
                $item
            );
        }
    }

    private function resolveDate($row, $dateIdx, $generalDateIdx, $override)
    {
        if ($override) return $override;
        $cols = [$dateIdx, $generalDateIdx, 3, 4, 7, 8];
        foreach ($cols as $idx) {
            $val = $row[$idx] ?? null;
            if (empty($val) || $val == '-') continue;
            if (is_numeric($val) && $val > 30000 && $val < 60000) {
                try {
                    return ExcelDate::excelToDateTimeObject($val)->format('Y-m-d');
                } catch (\Exception $e) {
                }
            }
            try {
                $clean = preg_replace('/[^\d\-\/\s:.]/', '', $val);
                if (empty($clean) || (is_numeric($clean) && strlen($clean) == 9)) continue;
                if (preg_match('/(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/', $clean, $m)) return "{$m[1]}-" . str_pad($m[2], 2, '0', STR_PAD_LEFT) . "-" . str_pad($m[3], 2, '0', STR_PAD_LEFT);
                if (preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $clean, $m)) return "{$m[3]}-" . str_pad($m[2], 2, '0', STR_PAD_LEFT) . "-" . str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $date = Carbon::parse($clean);
                if ($date->year >= 2000 && $date->year <= 2050) return $date->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }
        return null;
    }
}
