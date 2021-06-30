<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMssqlConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mssql-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test MSSQL Connection';

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
            \DB::connection('sqlsrv')->getPdo();

            $this->info('Connection Passed');
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e );
        }

        return 0;
    }
}
