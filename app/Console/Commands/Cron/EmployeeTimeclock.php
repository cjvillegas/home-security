<?php

namespace App\Console\Commands\Cron;

use App\Models\TimeClock;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeTimeclock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:fetch-timeclock-from-t-and-a';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch the timeclock data of employees from the T&A database and populate it inside the production DB.';

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
        // logs the execution
        $this->logExecution();

        $timeClockData = $this->getTimeclockData();
        dd($timeClockData);

        $chunkCounter = 0;

        // chunk the results to save memory
        foreach ($timeClockData->chunk(100) as $chunk) {
            // foreach to each instance of retrieved timeclock
            $newTimeclocks = [];
            foreach ($chunk as $timeClock) {
                // perform data sanitization
                $newTimeclocks[] = $this->sanitize((array) $timeClock);
            }

            // do the actual insertion of data
            TimeClock::insert($newTimeclocks);

            // increment the execution counter
            $chunkCounter++;

            // execution throttling
            // make the server rest after 10 bulk insert
            if ($chunkCounter % 10 === 0) {
                usleep(100000);
            }
        }
    }

    /**
     * Sanitize timeclock item coming from T&A
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $timeClock
     *
     * @return mixed
     */
    private function sanitize(array $timeClock)
    {
        // do a sanity check of the required data
        if (empty($timeClock['BlindId']) || empty($timeClock['OrderNo'])) {
            return false;
        }

        $sanitizedTimeClock['clock_num'] = $timeClock['BlindId'];
        $sanitizedTimeClock['swiped_at'] = $timeClock['BlindId'];

        return $sanitizedTimeClock;
    }

    /**
     * Retrieves the timeclock data from the time & attendance DB
     *
     * @return Collection
     */
    private function getTimeclockData(): Collection
    {
        $query = "
            SELECT
                TOP 10
                dbo.Employee.ClockNum,
                dbo.ClockTransactions.SwipeDateTime
            FROM
                dbo.ClockTransactions
            INNER JOIN
                dbo.Employee ON dbo.Employee.EmpID = dbo.ClockTransactions.EmpID
        ";

        // execute the query
        $timeClocks = DB::connection('time_clock_sql')->select($query);

        // return data as collection
        return collect($timeClocks);
    }

    /**
     * Logs the execution of this command. This is pretty useful
     * for bug tracking.
     *
     * @return void
     */
    private function logExecution(): void
    {
        Log::info('CRON for populating time_clocks table from T&A is RUNNING!!!',
            [
                'datetime' => now()->format('Y-m-d H:i:s'),
                'command' => 'employees:fetch-timeclock-from-t-and-a',
                'file' => 'EmployeeTimeclock'
            ]);

    }
}
