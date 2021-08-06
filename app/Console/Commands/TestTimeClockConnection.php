<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestTimeClockConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:time-clock-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the connection of the T&A database';

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
     * @return int
     */
    public function handle()
    {
        // Test database connection
        try {
            DB::connection('time_clock_sql')->getPdo();

            $test = DB::connection('time_clock_sql')->select($this->getSqlQuery());

            collect($test);

            $this->info('Connection successful');
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e );
        }
    }

    /**
     * The test query string
     *
     * @return string
     */
    private function getSqlQuery()
    {
        return "
            SELECT
                dbo.Employee.ClockNum,
                dbo.ClockTransactions.SwipeDateTime
            FROM
                dbo.ClockTransactions
                INNER JOIN dbo.Employee ON dbo.Employee.EmpID = dbo.ClockTransactions.EmpID"
        ;
    }
}
