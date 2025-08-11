<?php

namespace App\Jobs;

use App\Models\AreaResponsible;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAreaResponsiblePeopleCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $areaResponsibleId;

    public function __construct($areaResponsibleId)
    {
        $this->areaResponsibleId = $areaResponsibleId;
    }

    public function handle()
    {
        try {
            $areaResponsible = AreaResponsible::find($this->areaResponsibleId);
            if ($areaResponsible) {
                $areaResponsible->updatePeopleCount();
            }
        } catch (\Exception $e) {
            logger()->error('خطأ في Job تحديث عدد مسؤول المنطقة', [
                'area_responsible_id' => $this->areaResponsibleId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function failed(\Throwable $exception)
    {
        logger()->error('فشل Job تحديث عدد مسؤول المنطقة', [
            'area_responsible_id' => $this->areaResponsibleId,
            'error' => $exception->getMessage()
        ]);
    }
}
