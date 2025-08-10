<?php

namespace App\Jobs;

use App\Models\Block;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateBlockPeopleCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $blockId;

    public function __construct($blockId)
    {
        $this->blockId = $blockId;
    }

    public function handle()
    {
        try {
            $block = Block::find($this->blockId);
            if ($block) {
                $block->updatePeopleCount();
            }
        } catch (\Exception $e) {
            logger()->error('خطأ في Job تحديث عدد المندوب', [
                'block_id' => $this->blockId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function failed(\Throwable $exception)
    {
        logger()->error('فشل Job تحديث عدد المندوب', [
            'block_id' => $this->blockId,
            'error' => $exception->getMessage()
        ]);
    }
}