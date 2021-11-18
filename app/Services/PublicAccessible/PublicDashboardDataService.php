<?php

namespace App\Services\PublicAccessible;

use App\Models\Process;
use App\Models\Scanner;
use App\Models\ShiftAssignment;
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
        $scheduled = $this->getScheduledBlinds();

        $index = $this->filters['index'];

        $formatted = [];

        // loop through the processes, this will give us the static process data
        foreach ($processes as $process) {
            $item = [];
            $item['name'] = $process->name;
            if ($process->barcode === 'P1000') {
                $item['name'] = 'Tube Cut';
            }

            $item['scanners'] = [];
            $item['team_target'] = $index == 1 ? $process->team_trade_target : $process->team_internet_target;
            $item['hourly_target'] = round($scheduled / 7.5);
            $item['scheduled'] = $scheduled;
            $item['completed'] = 0;
            $item = array_merge($item, $this->generateKeysFromDates());

            // filter scanners based on process barcodes
            $filteredScanners = $scanners->filter(function ($value) use ($process) {
                if ($process->barcode === 'P1000' && $value->product_type === 'Vertical') {
                    $addi = false;
                } else {
                    $addi = true;
                }

                return $value->processid === $process->barcode && $addi;
            });

            // loop through each scanners data
            foreach ($filteredScanners as $scanner) {
                $item['scanners'][] = $scanner;
                $item['completed']++;

                // this is for the hourly data
                $scannedTime =  Carbon::parse($scanner->scannedtime, __env_timezone());
                $key = $scannedTime->format('H') . '-' . $scannedTime->clone()->addHour()->format('H');

                if (isset($item[$key])) {
                    $item[$key]++;
                }
            }

            $item['to_be_completed'] = $item['completed'] > $scheduled ? $item['completed'] : $scheduled - $item['completed'];
            $item['percentage'] = round(($item['completed'] / $item['team_target']) * 100) . '%';

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
        $dates = [$this->filters['start'], $this->filters['end']];

        return Scanner::
            select([
                'scanners.*',
                'o.product_type'
            ])
            ->join('orders as o', 'o.serial_id', 'scanners.blindid')
            ->whereIn('scanners.processid', self::PROCESS_CODES)
            ->whereBetween('scanners.scannedtime', $dates)
            ->groupBy('scanners.id')
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

    /**
     * Return the count of scheduled blinds
     *
     * @return int
     */
    private function getScheduledBlinds(): int
    {
        $folderNames = ["Roller - Shift {$this->filters['index']} Team 1", "Roller - Shift {$this->filters['index']} Team 2"];

        // if shift 1, append this folder names
        if ($this->filters['index'] == 1) {
            $folderNames[] = 'Roller Add - Shift 1 Team 1';
            $folderNames[] = 'Roller Add - Shift 1 Team 2';
        }

        $now = now(__env_timezone());

        return ShiftAssignment::whereIn('folder_name', $folderNames)
            ->where('work_date', $now->format('Y-m-d'))
            ->distinct()
            ->count('id');
    }
}
