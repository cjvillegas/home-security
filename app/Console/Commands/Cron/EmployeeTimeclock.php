<?php

namespace App\Console\Commands\Cron;

use App\Models\Employee;
use App\Models\TimeClock;
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

        $employees = Employee::select('id', 'user_id', 'fullname', 'clock_num')
            ->get();

        $chunkCounter = 0;

        // chunk the results to save memory
        foreach ($timeClockData->chunk(100) as $chunk) {
            // foreach to each instance of retrieved timeclock
            $newTimeclocks = [];
            foreach ($chunk as $timeClock) {
                // perform data sanitization
                $newTimeclocks[] = $this->sanitize((array) $timeClock, $employees);
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
     * @param Collection $employees
     *
     * @return mixed
     */
    private function sanitize(array $timeClock, Collection $employees)
    {
        // do a sanity check of the required data
        if (empty($timeClock['ClockNum']) || empty($timeClock['SwipeDateTime'])) {
            return false;
        }

        // fetch an employee record based on the t&a data's clock_num
        $employee = $employees->firstWhere('clock_num', $timeClock['ClockNum']);

        // build the data to be saved
        $sanitizedTimeClock['employee_id'] = optional($employee)->id;
        $sanitizedTimeClock['trans_id'] = $timeClock['TransID'];
        $sanitizedTimeClock['clock_num'] = $timeClock['ClockNum'];
        $sanitizedTimeClock['swiped_at'] = $timeClock['SwipeDateTime'];

        return $sanitizedTimeClock;
    }

    /**
     * Retrieves the timeclock data from the time & attendance DB
     *
     * @return Collection
     */
    public function getTimeclockData(): Collection
    {
        $latestTransId = (new TimeClock)->getLatestData();

        $query = "
            SELECT
                TOP 10000
                dbo.Employee.ClockNum,
                dbo.ClockTransactions.SwipeDateTime,
                dbo.ClockTransactions.TransID
            FROM
                dbo.ClockTransactions
            INNER JOIN
                dbo.Employee ON dbo.Employee.EmpID = dbo.ClockTransactions.EmpID
            ORDER BY
                dbo.ClockTransactions.TransID
        ";

        // if the table is already populated get the most latest timeclock
        if (!empty($latestTransId)) {
            $query .= "\t WHERE dbo.ClockTransactions.TransID > '{$latestTransId}'";
        }

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
