<?php

namespace App\Services\Reports;

use App\Models\Employee;
use App\Models\Export;
use App\Models\Process;
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
     * @var mixed
     */
    private $processes;

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
        $this->getProcesses();

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
                    $totalTradeTarget = 0;
                    $totalInternetTarget = 0;
                    $totalTradeNewJoiner = 0;
                    $totalInternetNewJoiner = 0;
                    $totalQcTagged = 0;
                    $totalScannersCount = 0;
                    $dateList = collect();

                    //each process must be assigned per date.
                    foreach ($dates as $date) {
                        $from = Carbon::parse($date)->format('Y-m-d'). ' '. '00:00:00';
                        $to = Carbon::parse($date)->format('Y-m-d'). ' '. '23:59:59';
                        $dateValue = Carbon::parse($date)->format('Y-m-d');
                        $value = [];
                        $qcCount = QcFault::whereBetween('operation_date', [$from, $to])
                            ->where('employee_id', $employee)
                            ->where('process_id', $process['process_id'])
                            ->count();
                        $performanceOnThatDate = $this->performancePerDateQuery($from, $to, $employee, $process['process_id']);
                        //if the Employee has performance on that date, assign the values

                        if ($performanceOnThatDate) {
                            $totalScannersCount += $performanceOnThatDate['scanners_count'];
                            $value = [
                                'name' => $performanceOnThatDate['name'],
                                'date' => $dateValue,
                                'scannedtime' => $performanceOnThatDate['scannedtime'],
                                'scanners_count' => $performanceOnThatDate['scanners_count'],
                                'qc_count' => $qcCount,
                                'trade_target' => $this->processes->where('id', $process['process_id'])->pluck('trade_target')[0] ?: 0,
                                'internet_target' => $this->processes->where('id', $process['process_id'])->pluck('internet_target')[0] ?: 0
                            ];

                            if ($this->isNewJoiner) {
                                $value = array_splice($value, 2);
                                $value['trade_target_new_joiner'] = $this->processes->where('id', $process['process_id'])->pluck('trade_target_new_joiner')[0] ?: 0;
                                $value['internet_target_new_joiner'] = $this->processes->where('id', $process['process_id'])->pluck('internet_target_new_joiner')[0] ?: 0;
                            }
                        }
                        //increment total target for Overall Employee performance
                        $totalTradeTarget += $this->processes->where('id', $process['process_id'])->pluck('trade_target')[0] ?: 0;
                        $totalInternetTarget += $this->processes->where('id', $process['process_id'])->pluck('internet_target')[0] ?: 0;
                        $totalTradeNewJoiner += $this->processes->where('id', $process['process_id'])->pluck('trade_target_new_joiner')[0] ?: 0;
                        $totalInternetNewJoiner += $this->processes->where('id', $process['process_id'])->pluck('internet_target_new_joiner')[0] ?: 0;
                        $totalQcTagged += $qcCount;

                        $dateList->push(
                            [
                                'date' => $date,
                                'data' => $value
                            ]
                        );
                    }

                    if ($this->getFilterValue('type') == 'trade' && $this->isNewJoiner) {
                        $totalQcPercentage = $totalTradeNewJoiner != 0 ? (number_format(($totalQcTagged/$totalTradeNewJoiner) * 100, 2, '.', ' ')) : 0;
                    }
                    if ($this->getFilterValue('type') == 'internet' && $this->isNewJoiner) {
                        $totalQcPercentage =  $totalInternetNewJoiner != 0 ? (number_format(($totalQcTagged/ $totalInternetNewJoiner) * 100, 2, '.', ' ')) : 0;
                    }
                    if ($this->getFilterValue('type') == 'trade' && !$this->isNewJoiner) {
                        $totalQcPercentage =  $totalTradeTarget != 0 ? (number_format(($totalQcTagged/ $totalTradeTarget) * 100, 2, '.', ' ')) : 0;
                    }
                    if ($this->getFilterValue('type') == 'internet' && !$this->isNewJoiner) {
                        $totalQcPercentage =  $totalInternetTarget != 0 ? (number_format(($totalQcTagged/ $totalInternetTarget) * 100, 2, '.', ' ')) : 0;
                    }
                    $processesData->push(
                        [
                            'process_name' => $process['name'],
                            'total_trade_target' => $totalTradeTarget,
                            'total_internet_target' => $totalInternetTarget,
                            'total_trade_target_new_joiner' => $totalTradeNewJoiner,
                            'total_internet_target_new_joiner' => $totalInternetNewJoiner,
                            'total_qc_tagged' => $totalQcTagged,
                            'total_scanners_count' => $totalScannersCount,
                            'trade_target_percentage' => $totalTradeTarget != 0 ? (number_format(($totalScannersCount/$totalTradeTarget) * 100, 2, '.', ' ')) : 0,
                            'internet_target_percentage' => $totalInternetTarget != 0 ? (number_format(($totalScannersCount/$totalInternetTarget) * 100, 2, '.', ' ')) : 0,
                            'trade_new_joiner_percentage' => $totalTradeNewJoiner != 0 ? (number_format(($totalScannersCount/$totalTradeNewJoiner) * 100, 2, '.', ' ')) : 0,
                            'internet_new_joiner_percentage' => $totalInternetNewJoiner != 0 ? (number_format(($totalScannersCount/$totalInternetNewJoiner) * 100, 2, '.', ' ')) : 0,
                            'total_qc_percentage' => $totalQcPercentage,
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

    public function getProcesses()
    {
        $this->processes = collect(Process::all());
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

    /**
     * Get the export type. The export type should have an Export counterpart.
     * Make sure you register a unique one in the Export model.
     *
     * @return string
     */
    public function exportType(): string
    {
        return Export::TARGET_PERFORMANCE_REPORT;
    }
}
