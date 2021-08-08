<?php

namespace App\Console\Commands\Retrofits;

use App\Models\Employee;
use App\Models\TimeClock;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class RetrofitEmployeeIdInEmployeeTimeclock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrofit:sync-employee-id-in-time-clocks-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Properly sync employee ID in time_clocks table with the proper ID in employees table based on the clock_num column in time__clocks table';

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
        $clockNums = $this->getTimeclockClockNumWithNoEmployeeID();
        $employees = $this->getEmployeesByClockNum($clockNums);

        $employees->each(function ($employee, $key) {
            TimeClock::
                where(function ($query) {
                    $query
                        ->whereNull('employee_id')
                        ->orWhere('employee_id', '');
                })
                ->where('clock_num', $employee->clock_num)
                ->update([
                    'employee_id' => $employee->id,
                    'updated_at' => now()
                ]);
        });
        return 0;
    }

    /**
     * This method retrieves the timeclock information with
     * where employee ID is empty
     *
     * @return array
     */
    private function getTimeclockClockNumWithNoEmployeeID(): array
    {
         return TimeClock::where(function ($query) {
            $query
                ->whereNull('employee_id')
                ->orWhere('employee_id', '');
        })
            ->groupBy('clock_num')
            ->get()
            ->pluck('clock_num')
            ->toArray();
    }

    /**
     * Retrieve list of employees based on the given clock_nums
     *
     * @return Collection
     */
    private function getEmployeesByClockNum(array $clockNums): Collection
    {
        return Employee::
            select('id', 'clock_num')
            ->whereIn('clock_num', $clockNums)
            ->get();
    }
}
