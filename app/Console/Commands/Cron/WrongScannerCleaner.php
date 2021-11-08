<?php

namespace App\Console\Commands\Cron;

use App\Models\Scanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WrongScannerCleaner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scanners:invalid-scanner-cleaner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes scanners with blindid length above or below 7.';

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
        Scanner::whereRaw(DB::raw("CHAR_LENGTH(scanners.blindid) != 7"))->delete();
    }
}
