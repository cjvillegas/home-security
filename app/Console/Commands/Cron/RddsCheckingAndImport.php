<?php

namespace App\Console\Commands\Cron;

use App\Models\Scanner;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class RddsCheckingAndImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:rdds-checking-and-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for RDDS products and import them again. RDDS are made twice and has two operations.';

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
     * @return bool
     */
    public function handle()
    {
        $now = now(__env_timezone());
        $isExecutable = $this->isExecutable($now);

        // sanity check: is reportable hour
        if (!$isExecutable) {
            return false;
        }

        $scanners = $this->getScanners($now);
        $nowUtc = now();
        foreach ($scanners->chunk(50) as $chunk) {
            $newScanners = [];
            foreach ($chunk as $scanner) {
                $item['scannedtime'] = $scanner->scannedtime;
                $item['employeeid'] = $scanner->employeeid;
                $item['processid'] = $scanner->processid;
                $item['blindid'] = $scanner->blindid;
                $item['machineid'] = $scanner->machineid;
                $item['created_at'] = $nowUtc;
                $item['updated_at'] = $nowUtc;
                $item['flag'] = 10;

                $newScanners[] = $item;
            }

            Scanner::insert($newScanners);

            // let the server rest for 0.1 second
            usleep(100000);
        }

        return true;
    }

    /**
     * Check if the current executed hours is reportable hour.
     * Since we are only running the process each and every end of shift
     * we need to determine if the current hour is really an hour after a shift.
     * Which means if the shift end is 13:59:59 its reportable hour is 14.
     *
     * @return bool
     */
    private function isExecutable(Carbon $now): bool
    {
        $isExecutable = false;
        $nowHour = $now->hour;

        // if the current executable hour is in the reportable hours
        // then continue the process
        if (in_array($nowHour, [6, 14, 22])) {
            $isExecutable = true;
        }

        return $isExecutable;
    }

    /**
     * Retrieve the scanners data.
     *
     * @return Collection
     */
    private function getScanners(Carbon $now): Collection
    {
        $shiftRange = $this->getShiftTimeRange($now);

        $scanners = Scanner::select([
           'scanners.scannedtime',
           'scanners.employeeid',
           'scanners.processid',
           'scanners.blindid'
        ])
        ->join('orders AS o', 'o.serial_id', 'scanners.blindid')
        ->where('o.blind_type', 'RDRS')
        ->filterInBetweenDates($shiftRange)
        ->groupBy('scanners.id')
        ->get();

        return $scanners;
    }

    /**
     * Get the reportable shift dates. The shift range fetched here will be based from the hour after the shift.
     * So for example the current reportable hour is 22 (which is a reportable hour), now we will get the shift
     * before that reportable hour which is shift two('14:00:00', '21:59:59'). This is because we are running the CRON
     * at the end of each shift.
     *
     * @param Carbon $now
     *
     * @return array
     */
    private function getShiftTimeRange(Carbon $now): array
    {
        $date = $now->format('Y-m-d');
        $hour = $now->hour;
        $dates = [];

        /**
         * the shift date generator logic
         * 14 => shift one
         * 22 => shift two
         * 6 => shift three
         */
        switch ($hour) {
            case 14:
                $shiftOne = Shift::SHIFT_ONE_TIME;
                $dates[] = "{$date} {$shiftOne[0]}";
                $dates[] = "{$date} {$shiftOne[1]}";
                break;
            case 22:
                $shiftTwo = Shift::SHIFT_TWO_TIME;
                $dates[] = "{$date} {$shiftTwo[0]}";
                $dates[] = "{$date} {$shiftTwo[1]}";
                break;
            case 6:
                $shiftThree = Shift::SHIFT_THREE_TIME;
                $prevDate = $now->clone()->subDay()->setHour(22)->format('Y-m-d');
                $dates[] = "{$prevDate} $shiftThree[0]";
                $dates[] = "{$date} $shiftThree[1]";
                break;
            default:
                break;
        }

        return $dates;
    }
}
