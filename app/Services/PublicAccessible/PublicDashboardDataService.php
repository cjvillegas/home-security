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
            $employeeCount = $this->getEmployeeWorkCount($process->barcode);

            $item = [];
            $item['name'] = $process->name;
            $item['scanners'] = [];
            $item['team_target'] = $index == 1 ? $process->team_trade_target : $process->team_internet_target;
            $item['hourly_target'] = $item['team_target'] / 7.5;

            // we need to make sure that the value is greater than 0 so we will not have an infinite result
            if ($employeeCount > 0) {
                $item['hourly_target'] = $item['hourly_target'] / $employeeCount;
            }

            // round the result
            $item['hourly_target'] = round($item['hourly_target']);

            $item['scheduled'] = 0;
            $item['completed'] = 0;
            $item['to_be_completed'] = $item['team_target'] ?? 0;
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
                $item['scheduled'] += $scanner->folder_name ? true : false;
                $item['completed']++;

                // this is for the hourly data
                $scannedTime =  Carbon::parse($scanner->scannedtime, __env_timezone());
                $key = $scannedTime->format('H') . '-' . $scannedTime->clone()->addHour()->format('H');

                if (isset($item[$key])) {
                    $item[$key]++;
                }
            }

            $item['to_be_completed'] -= $item['completed'] > $item['to_be_completed'] ? $item['to_be_completed'] : $item['completed'];
            $item['percentage'] = round(($item['completed'] / $item['team_target']) * 100, 2) . '%';

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
                'sa.folder_name',
                'o.product_type'
            ])
            ->leftJoin('shift_assignments AS sa', function ($join) use ($nowStart, $nowEnd) {
                $folderNames = ["Roller - Shift {$this->filters['index']} Team 1", "Roller - Shift {$this->filters['index']} Team 2"];

                // if shift 1, append this folder names
                if ($this->filters['index'] == 1) {
                    $folderNames[] = 'Roller Add - Shift 1 Team 1';
                    $folderNames[] = 'Roller Add - Shift 1 Team 2';
                }

                $join->on('sa.serial_id', 'scanners.blindid')
                    ->whereIn('folder_name', $folderNames)
                    ->whereBetween('work_date', [$nowStart, $nowEnd]);
            })
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
     * Get the number of employees that worked on a particular shift
     * and particular process
     *
     * @param string $process
     *
     * @return int
     */
    private function getEmployeeWorkCount(string $process): int
    {
        $dates = [$this->filters['start'], $this->filters['end']];

        return Scanner::where('processid', $process)
            ->select(['scanners.employeeid'])
            ->whereBetween('scanners.scannedtime', $dates)
            ->distinct()
            ->count('scanners.employeeid');
    }
}
