<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateExpiredProjects extends Command
{
    protected $signature = 'projects:update-expired';
    protected $description = 'Update expired projects status to completed';

    public function handle()
    {
        $updated = Project::where('end_date', '<', Carbon::today())
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        $this->info("تم تحديث {$updated} مشروع منتهي");
        return 0;
    }
}
