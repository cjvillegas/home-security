<?php

namespace App\Services\Reports;

use App\Models\Employee;
use App\Models\QcFault;
use App\Models\Scanner;
use App\Services\Reports\ReportDataService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection AS SupCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TargetPerformanceDataService extends ReportDataService
{
    /**
     *
     * @var bool
     */
    private $isNewJoiner = false;

    /**
     *
     * @var array
     */
    private $employees = [];

    /**
     * @var mixed
     */
    private $dateRange;


    /**
     * Target Performance Data constructor.
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
        $data = collect();

        $query = $this->buildQuery()->applyFilters();
        $this->employees = $this->getFilterValue('employees');
        $from = Carbon::parse($this->dateRange[0]);
        $to = Carbon::parse($this->dateRange[1]);
        $period = CarbonPeriod::create($from, $to);
        $dates = $period->toArray();

        switch ($type) {
            case 'list':
                $performances = $this->segregateByDays(
                    $query->getResultInCollection(),
                    $from,
                    $to,
                    $dates,
                    $this->employees
                );

                $data = collect(
                    [
                        'performances' => $performances,
                        'dates' => $dates
                    ]
                );
                break;

            case 'export':
                break;
        }

        return $data;
    }

    /**
     * Return base query of this service class
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $selectQuery = array();
        $this->isNewJoiner = $this->getFilterValue('isNewJoiner');

        $query = Scanner::query()
                ->select(
                    [
                        'e.id',
                        'e.fullname',
                        DB::raw("p.name AS name"),
                        DB::raw("p.id AS process_id"),
                        DB::raw("scanners.employeeid AS employeeid"),
                    ]
                )
                ->leftJoin('qc_faults', 'scanners.id', 'qc_faults.scanner_id')
                ->join('processes AS p', 'scanners.processid', 'p.barcode')
                ->join('employees AS e', 'scanners.employeeid', 'e.barcode')
                ->groupBy('p.barcode', 'scanners.employeeid')
                ->orderBy('scanners.employeeid');

        $this->query = $query;

        return $this;
    }

    /**
     * Logic for segregating the data by Employees and Days
     *
     * @return SupCollection
     */
    public function segregateByDays($performances, $from, $to, $dates, $employees): SupCollection
    {
        $data = collect();
        $performanceEmployeeTempHolder = clone $performances;
        // iterate each employee selected
        foreach ($employees as $employee) {
            $processesData = collect();
            $employeeName = Employee::find($employee);
            $employeeProcesses = $performanceEmployeeTempHolder->where('id', $employee);
            //iterate all processes done by employee
            if ($employeeProcesses->count() > 0) {
                foreach ($employeeProcesses as $process) {
                    $dateList = collect();
                    //each process must be assigned per date.
                    foreach ($dates as $date) {
                        $from = Carbon::parse($date)->format('Y-m-d'). ' '. '00:00:00';
                        $to = Carbon::parse($date)->format('Y-m-d'). ' '. '23:59:59';
                        $dateValue = Carbon::parse($date)->format('Y-m-d');
                        $value = [];

                        $performanceOnThatDate = $this->performancePerDateQuery($from, $to, $employee, $process['process_id']);
                        //if the Employee has performance on that date, assign the values
                        if ($performanceOnThatDate) {
                            $value = $this->isNewJoiner ? [
                                'name' => $performanceOnThatDate['name'],
                                'date' => $dateValue,
                                'scannedtime' => $performanceOnThatDate['scannedtime'],
                                'scanners_count' => $performanceOnThatDate['scanners_count'],
                                'qc_count' => 0,
                                'trade_target_new_joiner' => $performanceOnThatDate['trade_target_new_joiner'],
                                'internet_target_new_joiner' => $performanceOnThatDate['internet_target_new_joiner']
                            ] : [
                                'name' => $performanceOnThatDate['name'],
                                'date' => $dateValue,
                                'scannedtime' => $performanceOnThatDate['scannedtime'],
                                'scanners_count' => $performanceOnThatDate['scanners_count'],
                                'qc_count' => 0,
                                'trade_target' => $performanceOnThatDate['trade_target'],
                                'internet_target' => $performanceOnThatDate['internet_target']
                            ];
                        } else {
                            $value = $this->isNewJoiner ? [
                                'name' => $process['name'],
                                'date' => $dateValue,
                                'scannedtime' => null,
                                'scanners_count' => 0,
                                'qc_count' => 0,
                                'trade_target_new_joiner' => $process['trade_target_new_joiner'],
                                'internet_target_new_joiner' => $process['internet_target_new_joiner']
                            ] : [
                                'name' => $process['name'],
                                'date' => $dateValue,
                                'scannedtime' => null,
                                'scanners_count' => 0,
                                'qc_count' => 0,
                                'trade_target' => $process['trade_target'],
                                'internet_target' => $process['internet_target']
                            ];
                        }

                        $dateList->push(
                            [
                                'date' => $date,
                                'data' => $value
                            ]
                        );
                    }

                    $processesData->push(
                        [
                            'process_name' => $process['name'],
                            'data' => $dateList
                        ]
                    );
                }
            } else {
                $processesData = ['No Data available'];
            }


            $data->push(
                [
                    'employee_name' => $employeeName['fullname'],
                    'performances' => $processesData,
                    'is_new_joiner' => $this->isNewJoiner,
                    'type' => $this->getFilterValue('type'),
                ]
            );
        }
        return $data;
    }

    public function performancePerDateQuery($from, $to, $employee, $process)
    {
        $selectQuery = [
            'e.id',
            'e.fullname',
            'scanners.scannedtime',
            DB::raw('DATE(scanners.scannedtime) as date'),
            DB::raw("p.name AS name"),
            DB::raw("p.id AS process_id"),
            DB::raw("scanners.employeeid AS employeeid"),
            DB::raw("COUNT(scanners.id) AS scanners_count"),
            'p.trade_target',
            'p.internet_target'
        ];
        if ($this->isNewJoiner) {
            $selectQuery = array_splice($selectQuery, 2);
            array_push($selectQuery, 'p.trade_target_new_joiner', 'p.internet_target_new_joiner');
        }


        $data = Scanner::query()
            ->select($selectQuery)
            ->join('processes AS p', 'scanners.processid', 'p.barcode')
            ->join('employees AS e', 'scanners.employeeid', 'e.barcode')
            ->whereBetween('scanners.scannedtime', [$from, $to])
            ->where('e.id', $employee)
            ->where('p.id', $process)
            ->first();

        return $data;
    }

    /**
     * Apply the filters
     *
     * @return self
     */
    public function applyFilters(): self
    {
        $this->employees = $this->getFilterValue('employees');

        if ($this->isFilterExist('dateRange') && is_array($this->dateRange = $this->getFilterValue('dateRange'))) {
            $this->query->whereBetween('scanners.scannedtime', $this->dateRange)
                ->whereIn('e.id', $this->employees);
        }

        return $this;
    }
}
