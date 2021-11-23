<?php

namespace App\Console\Commands\Cron;

use App\Exports\Reports\PublicDashboardReportExport;
use App\Models\Shift;
use App\Notifications\PublicDashboardReportNotification;
use App\Services\PublicAccessible\PublicDashboardDataService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PublicDashboardHourlyReport extends Command
{
    /**
     * array
     */
    const EMAILS = [
        'rafael.diazalbertini@stylebyglobal.com',
        'guillermo.hurtado@stylebyglobal.com'
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:public-dashboard-hourly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email report to specified emails with our Public Dashboard Data';

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
        $now = now(__env_timezone());
        $shifts = Shift::SHIFT_TIME_LIST;

        $index = 1;
        $perShiftData = [];

        // loop through all the shift
        foreach ($shifts as $shift) {
            // get the start and end of shift
            $startAndEnd = $this->getShiftStartAndEnd($shift, $index);

            // Build the request instance. In here, we will emulate requesting
            $request = new Request();
            $request->merge([
                'index' => $index,
                'start' => $startAndEnd['start'],
                'end' => $startAndEnd['end']
            ]);

            // get the data
            $service = new PublicDashboardDataService($request->all());
            $data = $service->getData();
            $headers = array_merge($this->getDefaultHeader(), $this->generateShiftHeaders($startAndEnd['start'], $startAndEnd['end']));
            array_unshift($data, $headers);

            // before we do the export we should format and sort the data first based on our desired order
            // in this case we want it to follow the order based on our headers
            $perShiftData[] = array_map(function ($row) use ($headers) {
                $formatted = [];
                foreach ($headers as $key => $header) {
                    $formatted[$key] = $row[$key];
                }

                return  $formatted;
            }, $data);

            $index++;
        }

        $filePath = "public-dashboard/Public Dashboard.xlsx";
        ((new PublicDashboardReportExport(collect($perShiftData))))->store($filePath, 'public');

        $this->sendEmail($filePath);
    }

    /**
     * Notify these people of the report
     *
     * @param string $path
     *
     * @return void
     */
    private function sendEmail(string $path): void
    {
        // sanity check if the file already exists
        if (!Storage::disk()->exists($path)) {
            return;
        }

        $url = Storage::url($path);
        $emails = $this->getEmails();

        // loop through the emails
        foreach ($emails as $email) {
            Notification::route('mail', $email)
                ->notify(new PublicDashboardReportNotification($url));
        }

        // delete the generate file after the emails are sent
        Storage::delete($path);
    }

    /**
     * Get the shift's start and end date based on the current date
     *
     * @param array $shift
     * @param int $index
     *
     * @return string[]
     */
    public function getShiftStartAndEnd(array $shift, int $index): array
    {
        $now = now(__env_timezone());
        $hourNow = $now->hour;
        $date = ($hourNow < 6 && $hourNow > 0) ? $now->clone()->subDay()->format('Y-m-d') : $now->format('Y-m-d');
        $start = $date;
        $end = $date;

        switch ($index) {
            case 1:
                $start = "{$start} {$shift[0]}";
                $end = "{$end} {$shift[1]}";
                break;
            case 2:
                $start = "{$start} {$shift[0]}";
                $end = "{$end} {$shift[1]}";
                break;
            case 3:
                $nowAddOne = Carbon::parse($date)->addDay()->format('Y-m-d');
                $start = "{$start} {$shift[0]}";
                $end = "{$nowAddOne} {$shift[1]}";
                break;
            default:
        };

        return [
            'start' => $start,
            'end' => $end
        ];
    }

    /**
     * Default header
     *
     * @return string[]
     */
    public function getDefaultHeader()
    {
        return [
            'name' => 'P',
            'hourly_target' => 'HT',
            'scheduled' => 'S',
            'completed' => 'C',
            'to_be_completed' => 'TC',
            'percentage' => '%',
        ];
    }

    /**
     * Generate the shift headers. Shift headers are dynamic headers based on the shift's start and end time
     * i.e. 06-13:59 = [06, 07, 08, 09, 10, 11, 12, 13]
     *
     * @param string $start
     * @param string $end
     *
     * @return array
     */
    private function generateShiftHeaders(string $start, string $end) {
        $headers = [];
        $startCarbon = Carbon::parse($start);
        $endMoment = Carbon::parse($end);

        while ($startCarbon < $endMoment) {
            $label = $startCarbon->format('H');
            $key = "{$startCarbon->format('H')}-{$startCarbon->clone()->addHour()->format('H')}";
            $header[$key] = $label;

            array_push($headers, $header);

            $startCarbon = $startCarbon->clone()->addHour();
        }

        return array_merge(...$headers);
    }

    /**
     * @return false|string[]
     */
    private function getEmails()
    {
        if (__is_production()) {
            $emails = self::EMAILS;
        } else {
            $emails = explode(',', env('TEST_EMAIL'));
        }

        return $emails;
    }
}
