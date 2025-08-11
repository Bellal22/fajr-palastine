<?php
// app/Console/Commands/UpdateAreaResponsiblesPeopleCount.php

namespace App\Console\Commands;

use App\Models\AreaResponsible;
use Illuminate\Console\Command;

class UpdateAreaResponsiblesPeopleCount extends Command
{
    protected $signature = 'area-responsibles:update-people-count {--area_responsible_id= : تحديد مسؤول منطقة معين}';
    protected $description = 'تحديث عدد الأفراد لجميع مسؤولي المناطق أو مسؤول معين';

    public function handle()
    {
        try {
            $areaResponsibleId = $this->option('area_responsible_id');

            if ($areaResponsibleId) {
                // تحديث مسؤول منطقة واحد
                $areaResponsible = AreaResponsible::find($areaResponsibleId);
                if (!$areaResponsible) {
                    $this->error("مسؤول المنطقة رقم {$areaResponsibleId} غير موجود");
                    return 1;
                }

                $oldCount = $areaResponsible->people_count;
                $newCount = $areaResponsible->updatePeopleCount();

                $this->info("تم تحديث مسؤول المنطقة: {$areaResponsible->name}");
                $this->info("العدد السابق: {$oldCount} -> العدد الجديد: {$newCount}");
            } else {
                // تحديث جميع مسؤولي المناطق
                $areaResponsibles = AreaResponsible::all();
                $this->info("بدء تحديث عدد الأفراد لـ " . $areaResponsibles->count() . " مسؤول منطقة...");

                $bar = $this->output->createProgressBar($areaResponsibles->count());
                $bar->start();

                $totalUpdated = 0;
                foreach ($areaResponsibles as $areaResponsible) {
                    $oldCount = $areaResponsible->people_count;
                    $newCount = $areaResponsible->updatePeopleCount();

                    if ($oldCount !== $newCount) {
                        $totalUpdated++;
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();
                $this->info("تم الانتهاء! تم تحديث {$totalUpdated} مسؤول منطقة.");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('حدث خطأ: ' . $e->getMessage());
            logger()->error('خطأ في command تحديث عدد مسؤولي المناطق', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return 1;
        }
    }
}
