<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base class for our report data services
 *
 * @author Chaps
 */
abstract class ReportDataService implements ServiceDataInterface
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * @var mixed
     */
    protected $query;

    /**
     * @param string $type
     *
     * @return mixed
     */
    abstract public function getData(string $type);

    /**
     * @return self
     */
    abstract public function buildQuery();

    /**
     * @return self
     */
    abstract public function applyFilters();

    /**
     * ReportDataService constructor
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param int $page
     * @param int $size
     *
     * @return LengthAwarePaginator
     */
    protected function paginate(int $page = 1, int $size = 25): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }

    /**
     * Retrieve the qc fault data in collect format from the DB
     *
     * @return Collection
     */
    protected function getResultInCollection(): Collection
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
    protected function isFilterExist(string $key): bool
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
    protected function getFilterValue(string $key, $default = null)
    {
        return $this->filters[$key] ?? $default;
    }
}
