<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;
use App\Models\Scanner;
use Illuminate\Database\Eloquent\Collection;

class WorkAnalyticsDataService implements ServiceDataInterface
{
    /**
     * @var array
     */
    private $filters;

    /**
     * @var mixed
     */
    private $query;

    /**
     * WorkAnalyticsDataService constructor.
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
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
                    $scanners = $query->paginate($page, $size);
                } else {
                    $scanners = $query->getResultInCollection();
                }

                break;
            case 'export':
                $scanners = $query->getResultInCollection();

                break;
            case 'count';
                $scanners = $this->query->count();

                break;
            case 'query';
                $scanners = $this->query;

                break;

            case 'all';
                $scanners = $query->getResultInCollection();

                break;
        }

        return $scanners;
    }

    /**
     * Return base query of this service class
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $query = Scanner::query()
            ->select(
                'scanners.id',
                'scanners.scannedtime',
                'scanners.employeeid',
                'scanners.processid',
                'scanners.blindid',
                'e.id AS employee_id',
                'e.fullname AS fullname',
                'e.team_id',
                'e.shift_id',
                'p.barcode AS process_barcode',
                'p.id AS process_id'
            )
            ->join('processes AS p', 'p.barcode', '=', 'scanners.processid')
            ->join('employees AS e', 'e.barcode', '=', 'scanners.employeeid')
            ->groupBy('scanners.id')
            ->orderBy('scanners.id');

        $this->query = $query;

        return $this;
    }

    /**
     * Apply relative filters
     *
     * @return self
     */
    function applyFilters(): self
    {
        $start = $this->getFilterValue('start');
        $end = $this->getFilterValue('end');

        if ($this->isFilterExist('start') && $this->isFilterExist('end')) {
            $this->query->whereBetween('scannedtime', [$start, $end]);
        }

        if ($this->isFilterExist('employees')) {
            $this->query->byEmployees($this->getFilterValue('employees'));
        }

        if ($this->isFilterExist('processes')) {
            $this->query->byProcesses($this->getFilterValue('processes'));
        }

        return $this;
    }

    /**
     * Retrieve the qc fault data in collect format from the DB
     *
     * @return Collection
     */
    private function getResultInCollection(): Collection
    {
        return $this->query->get();
    }

    /**
     * Check certain key exists in the filters array
     *
     * @param string $key
     *
     * @return bool
     */
    private function isFilterExist(string $key): bool
    {
        return isset($this->filters[$key]);
    }

    /**
     * Retrieve a value from the filters using a key
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed|null
     */
    private function getFilterValue(string $key, $default = null)
    {
        return $this->filters[$key] ?? $default;
    }
}
