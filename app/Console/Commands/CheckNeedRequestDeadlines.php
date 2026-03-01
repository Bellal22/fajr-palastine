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
        $count = \App\Models\NeedRequestProject::checkAndExpire();
        $this->info("Successfully deactivated {$count} projects.");
    }
}
