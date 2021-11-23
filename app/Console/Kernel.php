<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        $schedule->command('orders:populate-orders-from-blind-data')
            ->everyThirtyMinutes()
            ->environments(['production', 'staging']);

        // fetches new orders from BLINDDATA. This CRON will only run when the env is production or staging
        $schedule->command('orders:populate-orders-from-blind-data --checking')
            ->dailyAt('02:00')
            ->environments(['production', 'staging'])
            ->timezone('BST');

        // runs a CRON daily to fetch data from the T&A database
        $schedule->command('employees:fetch-timeclock-from-t-and-a')
            ->dailyAt('00:00')
            ->environments(['production', 'staging']);

        // runs a CRON daily to fetch data from SAGE database
        $schedule->command('stocks:populate-stock-levels-from-sage')
            ->everyThirtyMinutes()
            ->environments(['production', 'staging']);

        // runs a CRON to fetch shift assignments
        $schedule->command('shifts:populate-shift-assignment-table')
            ->everyTwoHours()
            ->between('06:00', '17:00')
            ->environments(['production', 'staging']);

        // runs a cron that will delete a month old or older notifications
        $schedule->command('notifications:delete-a-month-old-notifications')
            ->dailyAt('00:00');

        // runs a cron that fetch Purchase Orders every 30 minutes from SAGE database
        $schedule->command('orders:populate-purchase-orders-from-sage')
            ->everyThirtyMinutes()
            ->environments(['production', 'staging']);

        // schedule to fetch invoiced data everyday @2pm
        $schedule->command('orders:invoiced-order')
            ->weekdays()
            ->everySixHours()
            ->environments(['production', 'staging']);

        // deletes invalid scanner data
        $schedule->command('scanners:invalid-scanner-cleaner')
            ->dailyAt('00:00');

        // checks for RDDS orders per shift and import them
        $schedule->command('orders:rdds-checking-and-import')
            ->hourlyAt('5');

        // public dashboard hourly report
        $schedule->command('reports:public-dashboard-hourly-report')
            ->hourly()
            ->timezone(__env_timezone());
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
