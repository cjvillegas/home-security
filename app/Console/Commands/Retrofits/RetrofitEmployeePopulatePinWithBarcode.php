<?php

namespace App\Console\Commands\Retrofits;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class RetrofitEmployeePopulatePinWithBarcode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrofit:employee-populate-pin-with-barcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will populate the employee\'s empty pin column with its corresponding barcode.';

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
        $employees = $this->getAllEmployeeWithEmptyPin();

        $counter = 0;

        // confirm execution
        if (!$this->confirm("Are you sure you want to proceed with this execution?")) {
            $this->info("Execution cancelled.");

            return;
        }

        $total = $employees->count();

        // if no employee to be updated, return and cancel the execution
        if (!$total) {
            $this->info('No employee to be updated.');

            return;
        }

        $bar = $this->output->createProgressBar($total);

        $bar->start();

        // chunk results for optimization
        foreach ($employees->chunk(100) as $chunk) {
            // loop through multiple employees
            foreach ($chunk as $employee) {
                // if employee's pin_code is empty, update it with the employee's barcode
                if (empty($employee->pin_code)) {
                    $employee->pin_code = $employee->barcode;
                    $employee->save();

                    $counter++;

                    $bar->advance();

                    // execution throttle
                    if ($counter % 100 === 0) {
                        usleep(1000);
                    }
                }
            }
        }

        $bar->finish();

        $this->info("\n Total records updated {$counter}");
    }

    /**
     * Retrieve list of employees where pin_code is empty
     *
     * @return Collection
     */
    private function getAllEmployeeWithEmptyPin()
    {
        return Employee::whereNull('pin_code')
            ->orWhere('pin_code', '')
            ->get();
    }
}
