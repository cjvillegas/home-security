<?php

namespace App\Console\Commands\Retrofits;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RetrofitTableColumn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrofit:db-column-value {table : The table name} {column : The table column} {value : The value to which the the column to be updated with.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will update a column\'s value in the specified table. Make sure you know what you are doing before executing this command. This command cannot be reverted nor creates a backup of all the data being updated';

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
        $table = $this->argument('table');
        $column = $this->argument('column');
        $value = $this->argument('value');

        if (!Schema::hasTable($table)) {
            $this->error("The specified {$table} table is not found.");

            return;
        }

        if (!Schema::hasColumn($table, $column)) {
            $this->error("The specified {$column} column in {$table} table is not found.");

            return;
        }

        $query = "UPDATE {$table} SET {$column} = {$value}";

        if ($this->confirm("You are about to execute this query `{$query}`. Are you sure what you are doing?")) {
            DB::table($table)->update([$column => $value]);

            $this->info("{$column} column in {$table} table has been successfully updated!");
        }
    }
}
