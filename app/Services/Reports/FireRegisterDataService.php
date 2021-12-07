<?php

namespace App\Services\Reports;

use App\Models\Employee;
use App\Models\Export;
use Carbon\Carbon;
use Illuminate\Support\Collection as SuppCollection;
use Illuminate\Support\Facades\Log;

class FireRegisterDataService extends ReportDataService
{
    /**
     * @var mixed
     */
    private $date = null;

    /**
     * @var mixed
     */
    private $from = null;

    /**
     * @var mixed
     */
    private $to = null;

    /**
     * FireRegisterDataService constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        parent::__construct($filters);
    }

    /**
     * Retrieved all Blinds based on selected DateRange
     *
     * @return SuppCollection
     */
    public function getData(string $type): SuppCollection
    {
        $data = collect();
        $this->date = Carbon::parse($this->getFilterValue('date'))->format('Y-m-d');
        $this->from = Carbon::parse($this->getFilterValue('date'))->format('Y-m-d'). ' '.$this->getFilterValue('from');
        //if selected shift is 3, it should fetch for tomorrow's date.
        $this->to = $this->getFilterValue('shifts') == 3 ? Carbon::parse($this->getFilterValue('date'))->addDay()->format('Y-m-d') .' '.$this->getFilterValue('to')
            : Carbon::parse($this->getFilterValue('date'))->format('Y-m-d').' '.$this->getFilterValue('to');

        $query = $this->buildQuery();
        switch ($type) {
            case 'list':
                $employees = $query->getResultInCollection();
                $data = $this->formatFetchedData($employees);

                break;
            case 'export':
                $employees = $query->getResultInCollection();
                $data = $this->formatFetchedData($employees);
                break;

        }
        return $data;
    }

    /**
     * Format fetched data from DB into collections
     *
     * @param  mixed $employees
     * @return SuppCollection
     */
    public function formatFetchedData($employees): SuppCollection
    {
        $data = collect();

        $employees->each(function ($employee, $employeeKey) use ($data) {
            $clockIn = isset($employee->clockIn->swiped_at) ? Carbon::parse($employee->clockIn->swiped_at) : null;
            $clockOut = isset($employee->clockOut->swiped_at) ? Carbon::parse($employee->clockOut->swiped_at) : null;

            $data->push(
                [
                    'id' => $employee->id,
                    'fullname' => $employee->fullname,
                    'scannedtime' => $employee->scannedtime,
                    'clock_num' => $employee->clock_num,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'time_in'=> !is_null($clockIn) || !is_null($clockIn) ? $clockIn->diff($clockOut)->format('%H:%I:%S') : null
                ]
            );
        });

        return $data;
    }

    /**
     * Get the base query for this report
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $date = $this->date;

        $query = Employee::query()
            ->select([
                'employees.id',
                'employees.fullname',
                'scanners.scannedtime',
                'employees.clock_num',
            ])
            ->with(['clockIn' => function ($query) use ($date) {
                $query->where('time_clocks.swiped_at', '>=', $date. ' 00:00:00')
                    ->where('time_clocks.swiped_at', '<=', $date. ' 23:59:59');
            }])
            ->with(['clockOut' => function ($query) use ($date) {
                $query->where('time_clocks.swiped_at', '>=', $date. ' 00:00:00')
                    ->where('time_clocks.swiped_at', '<=', $date. ' 23:59:59');
            }])
            ->join('scanners', 'employees.barcode', '=', 'scanners.employeeid')
            ->where('scannedtime', '>=', $this->from)
            ->where('scannedtime', '<=', $this->to)
            ->groupBy('employees.id', 'scanners.employeeid')
            ->orderBy('scanners.scannedtime', 'asc');

        $this->query = $query;

        return $this;
    }

    /**
     * Apply the present filters to the query.
     *
     * @return self
     */
    public function applyFilters(): self
    {
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
        return Export::FIRE_REGISTER_REPORT;
    }
}
