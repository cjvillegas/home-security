<?php

namespace App\Services\Reports;

use App\Models\TimeClock;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

    public function getData(string $type)
    {
        $query = $this->buildQuery()->applyFilters();

        switch ($type) {
            case 'list':
                $size = $this->getFilterValue('size', 25);
                $page = $this->getFilterValue('page', 1);

                // if pagination is enabled
                if ($this->isFilterExist('size')) {
                    $items = (clone $this->query)
                        ->get();

                    $timeClocks = new LengthAwarePaginator($items, 10, $size, $page);
                } else {
                    $timeClocks = $query->getResultInCollection();
                }

                break;
            case 'export':
                $timeClocks = $query->getResultInCollection();

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
     * Apply relative filters
     *
     * @return self
     */
    public function applyFilters(): self
    {
        // if employee filter is present
        if ($this->isFilterExist('dates') && $dates = $this->getFilterValue('dates')) {
            $this->query->filterInBetweenDates($dates);
        }

        // filter shift assignments by folder name
        if ($this->isFilterExist('employees') && !empty($employees = $this->getFilterValue('employees'))) {
            $this->query->filterPerEmployee($employees);
        }

        return $this;
    }
}
