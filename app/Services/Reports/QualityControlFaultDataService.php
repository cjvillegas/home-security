<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;
use App\Models\QcFault;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class QualityControlFaultDataService implements ServiceDataInterface
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
     * @var string[]
     */
    private $toSearchColumns = [
        'em.fullname',
        'qc.qc_code',
        'us.name',
        'pr.name',
        'qc_faults.description'
    ];

    /**
     * @var array
     */
    private $columns = [
        'qc_faults.id AS qc_fault_id',
        'qc_faults.description AS qc_fault_description',
        'qc_faults.created_at AS qc_fault_tag_at',
        'qc_faults.operation_date AS qc_fault_operation_date',
        'qc.id AS quality_control_id',
        'qc.qc_code AS quality_control_code',
        'em.fullname AS employee_full_name',
        'em.id AS employee_id',
        'us.id AS user_id',
        'us.name AS user_name',
        'pr.id AS process_id',
        'pr.name AS process_name',
        'sc.id AS scanner_id',
        'sc.blindid AS scanner_blind_id',
        'sc.scannedtime AS scanner_scanned_time',
    ];

    /**
     * QualityControlFaultDataService constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Retrieve the Qc Fault data, the return data will largely based on the
     * passed filters array
     *
     * @param string $type
     *
     * @return Collection|LengthAwarePaginator
     */
    public function getData(string $type)
    {
        $query = $this->buildQuery()->applyFilters();

        switch ($type) {
            case 'list':
                $size = $this->getFilterValue('size', 25);
                $page = $this->getFilterValue('page', 1);

                // if pagination is enabled
                if ($this->isFilterExist('size')) {
                    $qcFaults = $query->paginate($page, $size);
                } else {
                    $qcFaults = $query->getResultInCollection();
                }

                break;
            case 'export':
                $qcFaults = $query->getResultInCollection();

                break;
        }

        return $qcFaults;
    }

    /**
     * Get the base query for this report
     *
     * @return self
     */
    public function buildQuery(): self
    {
        // build the query
        $query = QcFault::query()
            ->select($this->columns)
            ->join('quality_controls AS qc', 'qc.id', 'qc_faults.quality_control_id')
            ->join('employees AS em', 'em.id', 'qc_faults.employee_id')
            ->join('users AS us', 'us.id', 'qc_faults.user_id')
            ->join('processes AS pr', 'pr.id', 'qc_faults.process_id')
            ->join('scanners AS sc', 'sc.id', 'qc_faults.scanner_id')
            ->groupBy('qc_faults.id')
            ->orderBy('qc_faults.id');

        $this->query = $query;

        return $this;
    }

    /**
     * Apply the present filters to the query.
     *
     * @return self
     */
    private function applyFilters(): self
    {
        $searchString = $this->getFilterValue('searchString');
        $dateRange = $this->getFilterValue('dateRange');

        // if employee filter is present
        if ($this->isFilterExist('employees')) {
            $this->query->filterByEmployee($this->getFilterValue('employees', []));
        }

        // if user filter is present
        if ($this->isFilterExist('users')) {
            $this->query->filterByUser($this->getFilterValue('users', []));
        }

        // if user filter is present
        if ($this->isFilterExist('processes')) {
            $this->query->filterByProcess($this->getFilterValue('processes', []));
        }

        // if user filter is present
        if ($this->isFilterExist('qualityControls')) {
            $this->query->filterByQualityControl($this->getFilterValue('qualityControls', []));
        }

        // if searchString is present
        if ($this->isFilterExist('searchString') && $searchString) {
            $this->query->filterBySearch($this->toSearchColumns, $searchString);
        }

        // if daterange is present
        if ($this->isFilterExist('dateRange') && $dateRange) {
            $this->query->filterInRange($dateRange);
        }

        return $this;
    }

    /**
     * Sets the columns that will be used for searching
     *
     * @param array $columns
     *
     * @return $this
     */
    public function setToSearchColumns(array $columns): self
    {
        $this->toSearchColumns = $columns;

        return $this;
    }

    /**
     * Set the columns that are to be retrieved from the DB
     *
     * @param array $columns
     *
     * @return $this
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param int $page
     * @param int $size
     *
     * @return LengthAwarePaginator
     */
    private function paginate(int $page = 1, int $size = 25): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
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
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
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