<?php

namespace App\Console\Commands\Cron;

use App\Abstracts\CronDatabasePopulator;
use App\Models\Employee;
use App\Models\TimeClock;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeTimeclock extends CronDatabasePopulator
{
    /**
     * @var Collection
     */
    private $employees;

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

        $this->employees = Employee::select('id', 'user_id', 'fullname', 'clock_num')
            ->get();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $timeClockData = $this->getDataFromBlind();

            $chunkCounter = 0;

            // chunk the results to save memory
            foreach ($timeClockData->chunk(100) as $chunk) {
                // foreach to each instance of retrieved timeclock
                $newTimeclocks = [];
                foreach ($chunk as $timeClock) {
                    // perform data sanitization
                    $sanitized = $this->sanitize((array) $timeClock);

                    // sanity check
                    if ($sanitized !== null) {
                        $newTimeclocks[] = $sanitized;
                    }
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
        } catch (Exception $error) {
            $this->sendFailedNotification('Employee Time Clock', $error);
        }
    }

    /**
     * Sanitize timeclock item coming from T&A
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $timeClock
     *
     * @return array|null
     */
    protected function sanitize(array $timeClock): ?array
    {
        // do a sanity check of the required data
        if (empty($timeClock['ClockNum']) || empty($timeClock['SwipeDateTime'])) {
            return null;
        }

        // fetch an employee record based on the t&a data's clock_num
        $employee = $this->employees->firstWhere('clock_num', $timeClock['ClockNum']);

        // build the data to be saved
        $sanitizedTimeClock['employee_id'] = optional($employee)->id;
        $sanitizedTimeClock['trans_id'] = $timeClock['TransID'];
        $sanitizedTimeClock['clock_num'] = $timeClock['ClockNum'];
        $sanitizedTimeClock['pay_rate'] = $timeClock['PayRate'];
        $sanitizedTimeClock['swiped_at'] = $timeClock['SwipeDateTime'];

        return $sanitizedTimeClock;
    }

    /**
     * Retrieves the timeclock data from the time & attendance DB
     *
     * @return Collection
     */
    public function getDataFromBlind(): Collection
    {
        $latestTransId = (new TimeClock)->getLatestData();

        $query = "
            SELECT
                TOP 10000
                dbo.Employee.ClockNum,
                dbo.ClockTransactions.SwipeDateTime,
                dbo.ClockTransactions.TransID,
                dbo.EmpExtra.PayRate
            FROM
                dbo.ClockTransactions
            INNER JOIN dbo.Employee ON dbo.Employee.EmpID = dbo.ClockTransaction0s.EmpID
            INNER JOIN dbo.EmpExtra ON dbo.EmpExtra.EmpID = dbo.Employee.EmpID
        ";

        // if the table is already populated get the most latest timeclock
        if (!empty($latestTransId)) {
            $query .= "\t WHERE dbo.ClockTransactions.TransID > '{$latestTransId}'";
        }

        $query .= "\n ORDER BY dbo.ClockTransactions.TransID";

        // execute the query
        $timeClocks = DB::connection('time_clock_sql')->select($query);

        // return data as collection
        return collect($timeClocks);
    }
}
