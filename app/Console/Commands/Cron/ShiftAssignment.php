<?php

namespace App\Console\Commands\Cron;

use App\Abstracts\CronDatabasePopulator;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShiftAssignment extends CronDatabasePopulator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shifts:populate-shift-assignment-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will populate the data of shift_assignments table from BlindData database.';

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
        try {
            $assignments = $this->getDataFromBlind();

            $chunkCounter = 0;

            // chunk the results to save memory
            foreach ($assignments->chunk(100) as $chunk) {
                // foreach to each instance of retrieved order
                $shiftAssignments = [];
                foreach ($chunk as $assignment) {
                    // perform insertion of the order
                    $shiftAssignments[] = $this->sanitize((array) $assignment);
                }

                // do the actual insertion of data
                ShiftAssignment::insert($shiftAssignments);

                // increment the execution counter
                $chunkCounter++;

                // execution throttling
                // make the server rest after 10 bulk insert
                if ($chunkCounter % 10 === 0) {
                    usleep(100000);
                }
            }
        } catch (Exception $error) {
            $this->sendFailedNotification('Shift Assignment', $error);
        }
    }

    /**
     * Get the shift assignment data from the BlidData database
     *
     * @return Collection
     */
    protected function getDataFromBlind(): Collection
    {
        $query = "
            SELECT DISTINCT
                sdl.id AS [SerialID],
                rt.Description AS [Team],
                od.ScheduledDate AS [ScheduledDate],
                CASE
                WHEN rt.Description LIKE '%Shift 1%' OR rt.Description LIKE 'ND%' THEN od.ScheduledDate
                WHEN DATEPART(DW, od.ScheduledDate) <> '2'AND rt.Description LIKE '%Shift 2%' THEN DATEADD(day, -1, od.ScheduledDate)
                WHEN DATEPART(DW, od.ScheduledDate) = '2'AND rt.Description LIKE '%Shift 2%' THEN DATEADD(day, -3, od.ScheduledDate)
                WHEN DATEPART(DW, od.ScheduledDate) <> '2'AND rt.Description LIKE '%Shift 3%' THEN DATEADD(day, -1, od.ScheduledDate)
                WHEN DATEPART(DW, od.ScheduledDate) = '2'AND rt.Description LIKE '%Shift 3%' THEN DATEADD(day, -3, od.ScheduledDate)
                WHEN DATEPART(DW, od.ScheduledDate) = '2'AND rt.Description LIKE '%Sat%' THEN DATEADD(day, -2, od.ScheduledDate)
                WHEN DATEPART(DW, od.ScheduledDate) = '2'AND rt.Description LIKE '%Sun%' THEN DATEADD(day, -1, od.ScheduledDate)
                END AS [WorkingDate]
            FROM
                dbo.[OrderDetail] od
                INNER JOIN dbo.[Order] o ON od.order_id = o.id
                INNER JOIN dbo.SerialDetailLine sdl ON od.id = sdl.orderdetail_id
                INNER JOIN dbo.BlindType bt ON bt.id = od.blindtype_id
                INNER JOIN dbo.ManLocation ml ON ml.id = bt.manlocation_id
                INNER JOIN dbo.RollerTable rt ON rt.id = od.RollerTableID
            WHERE
                o.order_id IS NOT NULL
                AND o.orderstatus_id <> '7'
                AND od.ScheduledDate IS NOT NULL
        ";

        // return data as collection
        return collect(DB::connection('sqlsrv')->select($query));
    }

    /**
     * Sanitize the data coming from the BlindData database.
     *
     * @param array $item
     *
     * @return array|bool
     */
    protected function sanitize(array $item): ?array
    {
        // do a sanity check of the required data
        if (empty($item['SerialID']) || empty($item['Team']) || empty($item['ScheduledDate'])) {
            return false;
        }

        $shiftAssignment['serial_id'] = $item['SerialID'];
        $shiftAssignment['folder_name'] = $item['Team'];
        $shiftAssignment['scheduled_date'] = (new Carbon(($item['ScheduledDate'])))->format('Y-m-d H:i:s');

        return $shiftAssignment;
    }
}
