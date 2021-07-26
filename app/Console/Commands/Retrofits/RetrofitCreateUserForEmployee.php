<?php

namespace App\Console\Commands\Retrofits;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class RetrofitCreateUserForEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrofit:create-user-per-employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user counterpart for each employee.';

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
        $employees = $this->getAllEmployeesWithNoUser();

        $counter = 0;

        if (!$this->confirm("Are you sure you want to proceed with this execution?")) {
            $this->info("Execution cancelled.");

            return;
        }

        $total = $employees->count();

        // if no employee to be processed, return and exit the execution
        if (!$total) {
            $this->info('No employee user to be created.');

            return;
        }

        $bar = $this->output->createProgressBar($total);

        $bar->start();

        // chunk results for optimization
        foreach ($employees->chunk(100) as $chunk) {
            // loop through multiple employees
            foreach ($chunk as $employee) {
                if (empty($employee->pin_code)) {
                    continue;
                }

                // generate a user
                $employee->generateUser();

                $counter++;

                // execution throttle
                if ($counter % 100 === 0) {
                    usleep(1000);
                }

                $bar->advance();
            }
        }

        $bar->finish();

        $this->info("\n Total records created {$counter}");
    }

    /**
     * Retrieve employees with empty user
     *
     * @return Collection
     */
    private function getAllEmployeesWithNoUser()
    {
        return Employee::where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', '');
            })
            ->get();
    }
}
