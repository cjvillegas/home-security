<?php

namespace App\Services\Reports;

use App\Models\Scanner;
use App\Services\Reports\ReportDataService;
use Illuminate\Support\Collection AS SupCollection;
use Illuminate\Support\Facades\DB;

class TargetPerformanceDataService extends ReportDataService
{
    /**
     *
     * @var bool
     */
    private $isNewJoiner = false;

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

        switch ($type) {
            case 'list':
                $performances = collect($query->getResultInCollection());
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
                'e.fullname',
                DB::raw("p.name AS name"),
                DB::raw("scanners.employeeid AS employeeid"),
                DB::raw("COUNT(scanners.id) AS scanners_count"),
                DB::raw("SUM(CASE WHEN qc_faults.id IS NOT NULL THEN 1 ELSE 0 END) AS qc_count"),
                'p.trade_target_new_joiner',
                'p.internet_target_new_joiner'
            ];
        } else {
            $selectQuery = [
                'e.fullname',
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
     * Apply the filters
     *
     * @return self
     */
    public function applyFilters(): self
    {
        $employees = $this->getFilterValue('employees');

        if ($this->isFilterExist('dateRange') && is_array($dateRange = $this->getFilterValue('dateRange'))) {
            $this->query->whereBetween('scanners.scannedtime', $dateRange)
                ->whereIn('e.id', $employees);
        }

        return $this;
    }
}
