<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckNeedRequestDeadlines extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'need-requests:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and deactivate need request projects that have passed their deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Deactivate expired projects
        $count = \App\Models\NeedRequestProject::checkAndExpire();
        if ($count > 0) {
            $this->info("Successfully deactivated {$count} projects.");
        }

        // 2. Notify about projects closing soon (in 48 hours)
        $this->info('Checking for need requests closing soon...');
        $closingSoon = \App\Models\NeedRequestProject::where('is_enabled', true)
            ->whereNotNull('deadline')
            ->where('deadline', '>', now())
            ->where('deadline', '<', now()->addHours(48))
            ->get();

        foreach ($closingSoon as $nrProject) {
            $supervisors = \App\Models\User::whereHas('needRequestSetting', function ($query) {
                $query->where('is_enabled', true);
            })->get();

            if ($supervisors->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($supervisors, new \App\Notifications\NeedRequestClosingSoonNotification($nrProject->project));
                $this->info("Notifications sent for project: {$nrProject->project->name}");
            }
        }
    }
}
