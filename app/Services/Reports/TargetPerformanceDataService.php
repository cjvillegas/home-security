<?php

namespace App\Services\Reports;

use App\Models\Employee;
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
                    collect($this->query->get()),
                    $this->dateRange[0],
                    $this->dateRange[1],
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
                DB::raw("scanners.employeeid AS employeeid"),
                DB::raw("COUNT(scanners.id) AS scanners_count"),
                DB::raw("SUM(CASE WHEN qc_faults.id IS NOT NULL THEN 1 ELSE 0 END) AS qc_count"),
                'p.trade_target_new_joiner',
                'p.internet_target_new_joiner'
            ];
        } else {
            $selectQuery = [
                'e.id',
                'e.fullname',
                'scanners.scannedtime',
                DB::raw("p.name AS name"),
                DB::raw("scanners.employeeid AS employeeid"),
                DB::raw("COUNT(scanners.id) AS scanners_count"),
                DB::raw("SUM(CASE WHEN qc_faults.id IS NOT NULL THEN 1 ELSE 0 END) AS qc_count"),
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

        foreach ($employees as $employee) {
            $employeeName = $performances->where('id', $employee)->first();
            $performance = collect();
            foreach ($dates as $date) {
                $performancesPerDate = $performances->where('id', $employee)
                    ->where('scannedtime', '>=', Carbon::parse($date)->format('Y-m-d'). ' '. '00:00:00')
                    ->where('scannedtime', '<=', Carbon::parse($date)->format('Y-m-d'). ' '. '23:59:59');

                $dateValue = Carbon::parse($date)->format('Y-m-d');

                if ($performancesPerDate->count() > 0) {
                    foreach ($performancesPerDate as $performancePerDate) {
                        Log::info($performancePerDate);
                        Log::info('-');
                        $value = $this->isNewJoiner ? [
                            'name' => $performancePerDate['name'],
                            'date' => $dateValue,
                            'scannedtime' => $performancePerDate['scannedtime'],
                            'qc_count' => $performancePerDate['qc_count'],
                            'scanners_count' => $performancePerDate['scanners_count'],
                            'trade_target_new_joiner' => $performancePerDate['trade_target_new_joiner'],
                            'internet_target_new_joiner' => $performancePerDate['internet_target_new_joiner']
                        ] : [
                            'name' => $performancePerDate['name'],
                            'date' => $dateValue,
                            'scannedtime' => $performancePerDate['scannedtime'],
                            'qc_count' => $performancePerDate['qc_count'],
                            'scanners_count' => $performancePerDate['scanners_count'],
                            'trade_target' => $performancePerDate['trade_target'],
                            'internet_target' => $performancePerDate['internet_target']
                        ];
                        //sanitize if the Employee has Data
                        if ($employeeName) {
                            $performance->push(
                                $value
                            );
                        }
                    }
                }
            }

            //sanitize if the Employee has Data
            if ($employeeName) {
                $data->push(
                    [
                        'employee_name' => $employeeName['fullname'],
                        'performances' => $performance
                    ]
                );
            }

        }

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
