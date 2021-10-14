<?php

namespace App\Services\Reports;

use App\Services\Reports\ReportDataService;

class TargetPerformanceDataService extends ReportDataService
{
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


    }

    /**
     * Return base query of this service class
     *
     * @return self
     */
    public function buildQuery(): self
    {
        return $this;
    }

    /**
     * Apply the filters
     *
     * @return self
     */
    public function applyFilters(): self
    {
        return $this;
    }
}
