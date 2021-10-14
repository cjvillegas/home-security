<?php

namespace App\Services\Reports;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
                $employees = $this->processData($query->getResultInCollection());

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
                'employees.barcode',
                'employees.shift_id'
            ])
            ->join('time_clocks AS tc', 'tc.employee_id', 'employees.id')
            ->join('scanners AS sc', function ($join) {
                $join->on('sc.employeeid', 'employees.barcode');

                if ($this->isFilterExist('date') && $this->getFilterValue('date')) {
                    $join->whereBetween('sc.scannedtime', $this->getStartAndEndDAte());
                }
            })
            ->with([
                'scanners' => function ($query) {
                    if ($this->isFilterExist('date') && $this->getFilterValue('date')) {
                        $query->filterInBetweenDates($this->getStartAndEndDAte());
                    }
                },
                'attendance' => function ($query) {
                    if ($this->isFilterExist('date') && $this->getFilterValue('date')) {
                        $date = $this->getDate();
                        $query->where('swiped_at', 'like', "{$date}%");
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
        if ($this->isFilterExist('date') && $this->getFilterValue('date')) {
            $date = $this->getDate();
            $this->query->where('swiped_at', 'like', "{$date}%");
        }

        // filter per employees
        if ($this->isFilterExist('employees')) {
            $this->query->whereIn('employees.id', $this->getFilterValue('employees'));
        }

        return $this;
    }

    /**
     * @param Collection $employees
     *
     * @return Collection
     */
    private function processData(Collection $employees): Collection
    {
        $data = [];

        $date = $this->getDate();
        $shiftTwoStart = Carbon::parse($date . " 18:00");

        foreach ($employees->chunk(100) as $chunk) {
            foreach ($chunk as $employee) {
                $newItem = [
                    'id' => $employee->id,
                    'shift_id' => $employee->shift_id,
                    'barcode' => $employee->barcode,
                    'clock_num' => $employee->clock_num,
                    'color' => $employee->color,
                    'fullname' => $employee->fullname,
                    'scanners' => [],
                    'attendance' => [],
                    'raw_attendance' => [],
                    'raw_scanners' => [],
                ];

                // filter scanners
                $scanners = $employee->scanners->filter(function ($value) use ($employee, $date, $shiftTwoStart) {
                    if ($employee->shift_id === 1 || $employee->shift_id === 2) {
                        return Carbon::parse($value->scannedtime)->format('Y-m-d') === $date;
                    }

                    if ($employee->shift_id === 3) {
                        $dates = $this->getStartAndEndDAte('Y-m-d');
                        $scannedTime = Carbon::parse($value->scannedtime);
                        $scannedFormatted = $scannedTime->format('Y-m-d');
                        $assumedBeforeShift = $scannedTime < $shiftTwoStart;

                        $isUnder12Hours = Carbon::parse($date . " 22:00:00")->diff($scannedTime)->h < 12;

                        return ($scannedFormatted === $dates[0] || $scannedFormatted === $dates[1]) && $isUnder12Hours && !$assumedBeforeShift;
                    }

                    return false;
                })->values();

                $attendance = $employee->attendance->filter(function($value) use ($employee, $date, $shiftTwoStart) {
                    if ($employee->shift_id === 1 || $employee->shift_id === 2) {
                        return Carbon::parse($value->swiped_at)->format('Y-m-d') === $date;
                    }

                    if ($employee->shift_id === 3) {
                        $dates = $this->getStartAndEndDAte('Y-m-d');
                        $swipedAt = Carbon::parse($value->swiped_at);
                        $swipedAtFormatted = $swipedAt->format('Y-m-d');
                        $assumedBeforeShift = $swipedAt < $shiftTwoStart;

                        $isUnder12Hours = Carbon::parse($date . " 22:00:00")->diff($swipedAt)->h < 12;

                        return ($swipedAtFormatted === $dates[0] || $swipedAtFormatted === $dates[1]) && $isUnder12Hours && !$assumedBeforeShift;
                    }

                    return false;
                })->values();

                // sanity check: the employee should have scanners and attendance
                if ($scanners->isEmpty() || $attendance->isEmpty()) {
                    continue;
                }

                $newItem['raw_scanners'] = $scanners;
                $newItem['raw_attendance'] = $attendance;

                // kick inside the first and last scan data
                $newItem['scanners'] = [
                    'first_scan' => $scanners[0] ?? null,
                    'last_scan' => $scanners->count() === 1 ? null : $scanners[$scanners->count() - 1] ?? null
                ];

                // record the clock in and clock out
                $newItem['attendance'] = [
                    'clock_in' => $attendance[0] ?? null,
                    'clock_out' => $attendance->count() === 1 ? null : $attendance[$attendance->count() - 1] ?? null
                ];

                $data[] = $newItem;
            }
        }

        return collect($data);
    }

    /**
     * @return array
     */
    private function getStartAndEndDAte($format = 'Y-m-d H:i'): array
    {
        $date = Carbon::parse($this->getDate());

        return [
            $date->startOfDay()->addHours(6)->format($format),
            $date->endOfDay()->addHours(6)->subMinute()->format($format)
        ];
    }

    /**
     * Get the filter date in Y-m-d format
     *
     * @return string
     */
    private function getDate(): string
    {
        return Carbon::parse($this->getFilterValue('date'))->format('Y-m-d');
    }
}
