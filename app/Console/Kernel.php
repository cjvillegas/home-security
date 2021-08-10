<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // fetches new orders from BLINDDATA. This CRON will only run when the env is production or staging
        $schedule->command('orders:populate-orders-from-sage')
            ->everyThirtyMinutes()
            ->environments(['production', 'staging']);

        // runs a CRON daily to fetch data from the T&A database
        $schedule->command('employees:fetch-timeclock-from-t-and-a')
            ->dailyAt('00:00')
            ->environments(['production', 'staging']);

        $schedule->command('stocks:populate-stockslevel-from-sage')
            ->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
