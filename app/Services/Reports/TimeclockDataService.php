<?php

namespace App\Services\Reports;

use App\Models\Export;
use App\Models\TimeClock;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Timeclock data service class.
 *
 * @author Chaps
 */
class TimeclockDataService extends ReportDataService
{
    /**
     * TimeclockDataService constructor.
     */
    public function __construct(array $filters)
    {
        parent::__construct($filters);
    }

    /**
     * Return the data based on the export type
     *
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Collection|LengthAwarePaginator|mixed
     */
    public function getData(string $type)
    {
        $query = $this->buildQuery()->applyFilters();

        switch ($type) {
            case 'list':
                $timeClocks = $this->processTimeclockData($query->getResultInCollection());

                break;
            case 'export':
                $timeClocks = $this->processTimeclockData($query->getResultInCollection());

                break;
        }

        return $timeClocks;
    }

    /**
     * Return base query of this service class
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $query = TimeClock::query()
            ->select([
                'time_clocks.id',
                'time_clocks.employee_id',
                'time_clocks.clock_num AS clock_number',
                'swiped_at',
                'em.fullname AS employee_name'
            ])
            ->join('employees AS em', 'em.id', 'time_clocks.employee_id')
            ->groupBy('time_clocks.id');

        $this->query = $query;

        return $this;
    }

    /**
     * Get the export type. The export type should have an Export counterpart.
     * Make sure you register a unique one in the Export model.
     *
     * @return string
     */
    public function exportType(): string
    {
        return Export::TIME_AND_ATTENDANCE_REPORT;
    }

    /**
     * Apply relative filters
     *
     * @return self
     */
    public function applyFilters(): self
    {
        // if employee filter is present
        if ($this->isFilterExist('date') && $date = $this->getFilterValue('date')) {
            $date = Carbon::parse($date);
            $dates = [$date->startOfDay()->format('Y-m-d H:i'), $date->endOfDay()->addHours('24')->format('Y-m-d H:i')];
            $this->query->filterInBetweenDates($dates);
        }

        // filter shift assignments by folder name
        if ($this->isFilterExist('employees') && !empty($employees = $this->getFilterValue('employees'))) {
            $this->query->filterPerEmployee($employees);
        }

        return $this;
    }

    /**
     * Processes timeclock data.
     *
     * @param Collection $collection
     *
     * @return Collection
     */
    private function processTimeclockData(Collection $collection): Collection
    {
        $timeclock = [];

        // chunk the data to avoid stack overflow
        foreach ($collection->chunk(100) as $chunk) {
            // loop through the actual data
            foreach ($chunk as $item) {
                // check if this item is not yet added in the timeclock array
                if (!isset($timeclock[$item->employee_id])) {
                    $timeclock[$item->employee_id]['employee_name'] = ucwords(strtolower($item->employee_name));
                    $timeclock[$item->employee_id]['clock_number'] = $item->clock_number;
                    $timeclock[$item->employee_id]['clock_in'] = date("M d, Y H:i", strtotime($item->swiped_at));
                    $timeclock[$item->employee_id]['clock_out'] = null;
                    $timeclock[$item->employee_id]['time_in'] = '--:--';
                } else {
                    $timeclock[$item->employee_id]['clock_out'] = date("M d, Y H:i", strtotime($item->swiped_at));
                }

                // if both clock_in & clock_out is not empty compute the time in
                if (!empty($timeclock[$item->employee_id]['clock_in']) && !empty($timeclock[$item->employee_id]['clock_out'])) {
                    $start = Carbon::parse($timeclock[$item->employee_id]['clock_in']);
                    $end = Carbon::parse($timeclock[$item->employee_id]['clock_out']);
                    $timeclock[$item->employee_id]['time_in'] = "{$start->diff($end)->h} hours";
                }
            }
        }

        return collect(array_values($timeclock));
    }
}
