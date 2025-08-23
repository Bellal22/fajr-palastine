<?php

namespace App\Console\Commands;

use App\Models\Person;
use Http;
use Illuminate\Console\Command;

class SyncPeopleToAPI extends Command
{
    protected $signature = 'sync:people-to-api {--limit=50 : عدد الأشخاص للمزامنة}';
    protected $description = 'مزامنة بيانات الأشخاص مع API الخارجي';

    public function handle()
    {
        $limit = $this->option('limit');

        $people = Person::whereNull('api_sync_status')
            ->orWhere('api_sync_status', 'failed')
            ->with(['block', 'relatives'])
            ->limit($limit)
            ->get();

        $this->info("بدء مزامنة {$people->count()} شخص...");

        $bar = $this->output->createProgressBar($people->count());
        $bar->start();

        $success = 0;
        $errors = 0;

        foreach ($people as $person) {
            try {
                // إعداد البيانات للإرسال
                $data = [
                    'pid' => $person->id_num ?? '',
                    'fname' => $person->first_name ?? '',
                    'sname' => $person->father_name ?? '',
                    'tname' => $person->grandfather_name ?? '',
                    'lname' => $person->family_name ?? '',
                    'fcount' => $person->relatives_count ?? 0,
                    'mob_1' => $person->phone ?? '',
                    'mob_2' => '', // مش موجود
                    'block' => $person->block_id ?? '',
                    'note' => $person->notes ?? 'تم المزامنة من Command',
                    'wife_id' => $person->getWifeId(),
                    'wife_name' => $person->getWifeName(),
                    'num_mail' => '', // مش موجود
                    'num_femail' => '', // مش موجود
                    'f_num_liss_3' => $person->getChildrenUnder3Count(),
                    'f_num_ill' =>'',
                    'f_num_sn' =>'',
                    'income' => '1', // افتراضي
                    'home_status' => $person->getHomeStatus(),
                    'date_of_birth' => $person->dob ? $person->dob->format('Y-m-d') : '',
                    'Original_governorate' => $person->original_governorate ?? '',
                    'marital_status' => $person->social_status ?? '',
                ];

                // إرسال البيانات للAPI
                $url = "https://aid.fajeryouth.org/public/API/convert/person/reg";
                $response = Http::timeout(30)->withHeaders([
                    'auth' => 'aaa@aaa@aaa@rrr',
                ])
                    ->asMultipart()
                    ->post($url, $data);

                if ($response->successful()) {
                    // تحديث حالة المزامنة للنجاح
                    $person->update([
                        'api_synced_at' => now(),
                        'api_sync_status' => 'success'
                    ]);

                    $success++;
                    $this->line("✅ نجح: {$person->first_name} {$person->family_name}");
                } else {
                    // تحديث حالة المزامنة للفشل
                    $person->update([
                        'api_sync_status' => 'failed',
                        'api_sync_error' => $response->body()
                    ]);

                    $errors++;
                    $this->error("❌ فشل: {$person->first_name} - كود الخطأ: {$response->status()}");
                }
            } catch (\Exception $e) {
                // تسجيل الخطأ في قاعدة البيانات
                $person->update([
                    'api_sync_status' => 'failed',
                    'api_sync_error' => $e->getMessage()
                ]);

                $errors++;
                $this->error("💥 استثناء في {$person->first_name}: " . $e->getMessage());

                // تسجيل الخطأ في الـ log
                logger()->error('خطأ في مزامنة الشخص من Command', [
                    'person_id' => $person->id,
                    'person_name' => $person->first_name . ' ' . $person->family_name,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }

            $bar->advance();
            usleep(500000); // تأخير نصف ثانية
        }

        $bar->finish();
        $this->newLine();
        $this->info("انتهت المزامنة: {$success} نجح، {$errors} فشل");
    }
}
