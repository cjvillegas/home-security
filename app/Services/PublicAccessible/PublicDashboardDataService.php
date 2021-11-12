<?php

namespace App\Services\PublicAccessible;

use App\Models\Process;
use App\Models\Scanner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PublicDashboardDataService
{
    /**
     *
     */
    const PROCESS_CODES = ['P1000', 'P1005', 'P1003', 'P1004', 'P1002'];

    /**
     * @var array
     */
    private $filters;

    /**
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getData()
    {
        // the roller processes and sort it based on the designed sort_order
        $processes = $this->getProcesses()->sortBy('sort_order');
        $scanners = $this->getScanners();
        $index = $this->filters['index'];

        $formatted = [];

        // loop through the processes, this will give us the static process data
        foreach ($processes as $process) {
            $item = [];
            $item['name'] = $process->name;
            $item['scanners'] = $scanners;
            $item['internet_target'] = $index == 1 ? $process->trade_target : $process->internet_target;
            $item['hourly_target'] = round($item['internet_target'] / 7.5, 0);
            $item['scheduled'] = 0;
            $item['completed'] = 0;
            $item['to_be_completed'] = $process->internet_target ?? 0;
            $item = array_merge($item, $this->generateKeysFromDates());

            // loop through each scanners data
            foreach ($scanners as $scanner) {
                $item['scheduled'] += $scanner->folder_name ? true : false;
                $item['completed'] += $scanner->processid == $process->barcode ? true : false;

                // this is for the hourly data
                $scannedTime =  Carbon::parse($scanner->scannedtime, __env_timezone());
                $key = $scannedTime->format('H') . '-' . $scannedTime->clone()->addHour()->format('H');

                if (isset($item[$key])) {
                    $item[$key]++;
                }
            }

            $item['to_be_completed'] -= $item['completed'] > $item['to_be_completed'] ? $item['to_be_completed'] : $item['completed'];

            $formatted[] = $item;
        }

        return $formatted;
    }

    /**
     * Generate empty array from given dates
     *
     * @return array
     */
    private function generateKeysFromDates(): array
    {
        $start = Carbon::parse($this->filters['start']);
        $end = Carbon::parse($this->filters['end']);
        $hours = [];

        while ($start < $end) {
            $key = $start->format('H') . '-' . $start->clone()->addHour()->format('H');
            $hours[$key] = 0;

            $start->addHour();
        }

        return $hours;
    }

    /**
     * Get the orders
     *
     * @return Collection
     */
    private function getScanners(): Collection
    {
        $now = now(__env_timezone());
        $nowStart = $now->clone()->startOfDay()->toDateTimeString();
        $nowEnd = $now->clone()->endOfDay()->toDateTimeString();
        $dates = [$this->filters['start'], $this->filters['end']];

        return Scanner::
            select([
                'scanners.*',
                'sa.folder_name'
            ])
            ->leftJoin('shift_assignments AS sa', function ($join) use ($nowStart, $nowEnd) {
                $join->on('sa.serial_id', 'scanners.blindid')
                    ->whereIn('folder_name', ["Shift {$this->filters['index']} Team 1", "Shift {$this->filters['index']} Team 2"])
                    ->whereBetween('work_date', [$nowStart, $nowEnd]);
            })
            ->whereIn('scanners.processid', self::PROCESS_CODES)
            ->whereBetween('scanners.scannedtime', $dates)
            ->groupBy('scanners.blindid')
            ->orderBy('scanners.scannedtime', 'asc')
            ->get();
    }

    /**
     * Get rollers processes
     *
     * @return Collection
     */
    private function getProcesses(): Collection
    {
        return Process::whereIn('barcode', self::PROCESS_CODES)
            ->select([
                'processes.*',
                DB::raw("(
                    CASE
                        WHEN barcode = 'P1000' THEN 0
                        WHEN barcode = 'P1005' THEN 1
                        WHEN barcode = 'P1003' THEN 2
                        WHEN barcode = 'P1004' THEN 3
                        WHEN barcode = 'P1002' THEN 4
                        ELSE 5
                    END
                    ) as sort_order")
            ])
            ->get();
    }
}
