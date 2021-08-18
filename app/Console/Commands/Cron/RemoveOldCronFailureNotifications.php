<?php

namespace App\Console\Commands\Cron;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveOldCronFailureNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:delete-a-month-old-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will delete all cron notifications that are month old or greater.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // Runs the query that will delete a month old or so notifications
        Notification::where("type", "LIKE", "%CronFailureNotification")
            ->whereRaw(DB::raw('created_at < (NOW() - INTERVAL 1 MONTH)'))
            ->delete();
    }
}
