<?php

namespace App\Console\Commands;

use App\Models\Block;
use Illuminate\Console\Command;

class UpdateBlocksPeopleCount extends Command
{
    protected $signature = 'blocks:update-people-count {--block_id= : تحديد مندوب معين}';
    protected $description = 'تحديث عدد الأفراد لجميع المندوبين أو مندوب معين';

    public function handle()
    {
        try {
            $blockId = $this->option('block_id');

            if ($blockId) {
                // تحديث مندوب واحد
                $block = Block::find($blockId);
                if (!$block) {
                    $this->error("المندوب رقم {$blockId} غير موجود");
                    return 1;
                }

                $oldCount = $block->people_count;
                $newCount = $block->updatePeopleCount();

                $this->info("تم تحديث المندوب: {$block->name}");
                $this->info("العدد السابق: {$oldCount} -> العدد الجديد: {$newCount}");
            } else {
                // تحديث جميع المندوبين
                $blocks = Block::all();
                $this->info("بدء تحديث عدد الأفراد لـ " . $blocks->count() . " مندوب...");

                $bar = $this->output->createProgressBar($blocks->count());
                $bar->start();

                $totalUpdated = 0;
                foreach ($blocks as $block) {
                    $oldCount = $block->people_count;
                    $newCount = $block->updatePeopleCount();

                    if ($oldCount !== $newCount) {
                        $totalUpdated++;
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();
                $this->info("تم الانتهاء! تم تحديث {$totalUpdated} مندوب.");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('حدث خطأ: ' . $e->getMessage());
            logger()->error('خطأ في command تحديث عدد المندوبين', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return 1;
        }
    }
}
