<?php

namespace App\Services\Reports;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WhoWorksHereDataService extends ReportDataService
{
    /**
     * TimeclockDataService constructor.
     */
    public function __construct(array $filters)
    {
        parent::__construct($filters);
    }

    /**
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getData(string $type)
    {
        $query = $this->buildQuery()->applyFilters();

        switch ($type) {
            case 'list':
                $employees = $query->getResultInCollection();

                break;
        }

        return $employees;
    }

    /**
     * Return base query of this service class
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $query = Employee::query()
            ->select([
                'employees.id',
                'employees.fullname',
                'employees.clock_num',
                'employees.barcode'
            ])
            ->join('time_clocks AS tc', 'tc.employee_id', 'employees.id')
            ->join('scanners AS sc', function ($join) {
                $join->on('sc.employeeid', 'employees.barcode');

                if ($this->isFilterExist('date') && $date = $this->getFilterValue('date')) {
                    $date = Carbon::parse($date);
                    $dates = [$date->startOfDay()->format('Y-m-d H:i'), $date->endOfDay()->addHours('24')->format('Y-m-d H:i')];
                    $join->whereBetween('sc.scannedtime', [$dates]);
                }
            })
            ->with([
                'scanners' => function ($query) {
                    if ($this->isFilterExist('date') && $date = $this->getFilterValue('date')) {
                        $date = Carbon::parse($date);
                        $dates = [$date->startOfDay()->format('Y-m-d H:i'), $date->endOfDay()->addHours('24')->format('Y-m-d H:i')];
                        $query->filterInBetweenDates($dates);
                    }
                },
                'attendance' => function ($query) {
                    if ($this->isFilterExist('date') && $date = $this->getFilterValue('date')) {
                        $date = Carbon::parse($date);
                        $dates = [$date->startOfDay()->format('Y-m-d H:i'), $date->endOfDay()->addHours('24')->format('Y-m-d H:i')];
                        $query->filterInBetweenDates($dates);
                    }
                }
            ])
            ->groupBy('employees.id');

        $this->query = $query;

        return $this;
    }

    /**
     * Apply the filters
     *
     * @return self
     */
    public function applyFilters(): self
    {
        // if employee filter is present
        if ($this->isFilterExist('date') && $date = $this->getFilterValue('date')) {
            $date = Carbon::parse($date);
            $dates = [$date->startOfDay()->format('Y-m-d H:i'), $date->endOfDay()->addHours('24')->format('Y-m-d H:i')];
            $this->query->whereBetween('tc.swiped_at', [$dates]);
        }

        return $this;
    }
}
