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
        $performances = collect();
        $query = $this->buildQuery()->applyFilters();
        $this->employees = $this->getFilterValue('employees');

        switch ($type) {
            case 'list':
                $performances = $this->segregateByDays(
                    $query->getResultInCollection(),
                    Carbon::parse($this->dateRange[0])->addDay(),
                    Carbon::parse($this->dateRange[1])->addDay(),
                    $this->employees
                );
                break;

            case 'export':
                break;
        }

        return $performances;
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

        if ($this->isNewJoiner) {
            $selectQuery = [
                'e.id',
                'e.fullname',
                'scanners.scannedtime',
                DB::raw("p.name AS name"),
                DB::raw("p.id AS process_id"),
                DB::raw("scanners.employeeid AS employeeid"),
                DB::raw("COUNT(scanners.id) AS scanners_count"),
                'p.trade_target_new_joiner',
                'p.internet_target_new_joiner'
            ];
        } else {
            $selectQuery = [
                'e.id',
                'e.fullname',
                'scanners.scannedtime',
                DB::raw("p.name AS name"),
                DB::raw("p.id AS process_id"),
                DB::raw("scanners.employeeid AS employeeid"),
                DB::raw("COUNT(scanners.id) AS scanners_count"),
                'p.trade_target',
                'p.internet_target'
            ];
        }

        $query = Scanner::query()
                ->select($selectQuery)
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
    public function segregateByDays($performances, $from, $to, $employees): SupCollection
    {
        $data = collect();
        $period = CarbonPeriod::create($from, $to);
        foreach ($employees as $date) {
        }

        $dates = $period->toArray();

        // new logic
        foreach ($employees as $employee) {
            $employeeName = Employee::find($employee);
            $performancesPerProcesses = $performances->where('id', $employee);
            $performanceGroupByProcess = collect();
            $performanceGroupByDate = collect();
            $validDate = null;

            $performancesPerProcesses->each(function ($performanceProcess, $performanceProcessKey) use ($dates, $employee, $employeeName, $performanceGroupByDate, $performanceGroupByProcess, $performances) {

                foreach ($dates as $date) {
                    $from = Carbon::parse($date)->format('Y-m-d'). ' '. '00:00:00';
                    $to = Carbon::parse($date)->format('Y-m-d'). ' '. '23:59:59';
                    $dateValue = Carbon::parse($date)->format('Y-m-d');
                    $qcFaultsCount = QcFault::filterByEmployee($employee)
                            ->filterInRange([$from, $to])
                            ->filterByProcess([$performanceProcess['process_id']])
                            ->count();
                    $performancePerDate = $performances
                        ->where('id', $employee)
                        ->where('name', $performanceProcess['name'])
                        ->where('scannedtime', '>=', $from)
                        ->where('scannedtime', '<=', $to)->first();
                    // check if the Process has data for that specific date
                    if (!empty($performancePerDate)) {
                        if ($performanceProcess['name'] == $performancePerDate['name']) {

                            $value = $this->isNewJoiner ? [
                                'name' => $performancePerDate['name'],
                                'date' => $dateValue,
                                'scannedtime' => $performancePerDate['scannedtime'],
                                'scanners_count' => $performancePerDate['scanners_count'],
                                'qc_count' => $qcFaultsCount,
                                'trade_target_new_joiner' => $performancePerDate['trade_target_new_joiner'],
                                'internet_target_new_joiner' => $performancePerDate['internet_target_new_joiner']
                            ] : [
                                'name' => $performancePerDate['name'],
                                'date' => $dateValue,
                                'scannedtime' => $performancePerDate['scannedtime'],
                                'scanners_count' => $performancePerDate['scanners_count'],
                                'qc_count' => $qcFaultsCount,
                                'trade_target' => $performancePerDate['trade_target'],
                                'internet_target' => $performancePerDate['internet_target']
                            ];
                        }
                    } else {
                        $value = $this->isNewJoiner ? [
                            'name' => null,
                            'date' => $dateValue,
                            'scannedtime' => null,
                            'scanners_count' => 0,
                            'qc_count' => null,
                            'trade_target_new_joiner' => null,
                            'internet_target_new_joiner' => null
                        ] : [
                            'name' => null,
                            'date' => $dateValue,
                            'scannedtime' => null,
                            'scanners_count' => 0,
                            'qc_count' => null,
                            'trade_target' => null,
                            'internet_target' => null
                        ];
                    }

                    $performanceGroupByDate->push(
                        $value
                    );
                }

                $performanceGroupByProcess->push(
                    [
                        'process_name' => $performanceProcess['name'],
                        'data' => $performanceGroupByDate->filter(function ($performance) use ($performanceProcess) {
                            return $performance['name'] == $performanceProcess['name'] || $performance['name'] == null;
                        })->unique(function ($performance) {
                            return $performance['date'].$performance['scanners_count'];
                        })->sortByDesc('scanners_count')
                    ]
                );
                //reset its value each iteration to avoid data redundancy
                $performanceGroupByDate = collect();
            });
            //sanitize if the Employee has Data
            $data->push(
                [
                    'employee_name' => $employeeName['fullname'],
                    'performances' => $performanceGroupByProcess
                ]
            );
            $performanceGroupByProcess = collect();

        }

        // foreach ($employees as $employee) {
        //     $employeeName = $performances->where('id', $employee)->first();
        //     $performance = collect();
        //     $performanceGroupByDate = collect();

        //     foreach ($dates as $date) {
        //         $from = Carbon::parse($date)->format('Y-m-d'). ' '. '00:00:00';
        //         $to = Carbon::parse($date)->format('Y-m-d'). ' '. '23:59:59';

        //         $performancesPerDate = $performances->where('id', $employee)
        //             ->where('scannedtime', '>=', $from)
        //             ->where('scannedtime', '<=', $to);

        //         $dateValue = Carbon::parse($date)->format('Y-m-d');

        //         if ($performancesPerDate->count() > 0) {
        //             foreach ($performancesPerDate as $performancePerDate) {
        //                 $qcFaultsCount = QcFault::filterByEmployee($employee)
        //                     ->filterInRange([$from, $to])
        //                     ->filterByProcess([$performancePerDate['process_id']])
        //                     ->count();
        //                 $value = $this->isNewJoiner ? [
        //                     'name' => $performancePerDate['name'],
        //                     'date' => $dateValue,
        //                     'scannedtime' => $performancePerDate['scannedtime'],
        //                     'scanners_count' => $performancePerDate['scanners_count'],
        //                     'qc_count' => $qcFaultsCount,
        //                     'trade_target_new_joiner' => $performancePerDate['trade_target_new_joiner'],
        //                     'internet_target_new_joiner' => $performancePerDate['internet_target_new_joiner']
        //                 ] : [
        //                     'name' => $performancePerDate['name'],
        //                     'date' => $dateValue,
        //                     'scannedtime' => $performancePerDate['scannedtime'],
        //                     'scanners_count' => $performancePerDate['scanners_count'],
        //                     'qc_count' => $qcFaultsCount,
        //                     'trade_target' => $performancePerDate['trade_target'],
        //                     'internet_target' => $performancePerDate['internet_target']
        //                 ];
        //                 //sanitize if the Employee has Data
        //                 if ($employeeName) {
        //                     $performance->push(
        //                         $value
        //                     );
        //                 }
        //             }
        //         } else {
        //             if ($employeeName) {
        //                 $performance->push(
        //                     $value = $this->isNewJoiner ? [
        //                         'name' => null,
        //                         'date' => null,
        //                         'scannedtime' => null,
        //                         'scanners_count' => null,
        //                         'qc_count' => null,
        //                         'trade_target_new_joiner' => null,
        //                         'internet_target_new_joiner' => null
        //                     ] : [
        //                         'name' => null,
        //                         'date' => null,
        //                         'scannedtime' => null,
        //                         'scanners_count' => null,
        //                         'qc_count' => null,
        //                         'trade_target' => null,
        //                         'internet_target' => null
        //                     ]
        //                 );
        //             }
        //         }

        //         $performanceGroupByDate->push(
        //             [
        //                 'date' => $date,
        //                 'data' => $performance
        //             ]
        //         );
        //         $performance = collect();
        //     }

        //     //sanitize if the Employee has Data
        //     if ($employeeName) {
        //         $data->push(
        //             [
        //                 'employee_name' => $employeeName['fullname'],
        //                 'performances' => $performanceGroupByDate
        //             ]
        //         );
        //     }

        // }

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
