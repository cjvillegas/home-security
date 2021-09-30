<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class ManufacturedBlindDataService implements ServiceDataInterface
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
     * TeamStatusDataService constructor.
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Retrieve Manufactured Blinds Data, the return data will largely based on the
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
                    $manufacturedBlinds = $query->paginate($page, $size);
                } else {
                    $manufacturedBlinds = $query->getResultInCollection();
                }

                break;
            case 'export':
                $manufacturedBlinds = $query->getResultInCollection();

                break;
        }

        return $manufacturedBlinds;
    }

    /**
     * Query
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $query = Order::query()
            ->select([
                'orders.id',
                'sc.scannedtime',
                'orders.serialid'
            ])
            ->leftJoin('scanners AS sc', 'orders.serial_id', 'sc.blindid')
            ->groupBy('orders.id');

        $this->query = $query;

        return $this;

    }

    public function applyFilters()
    {
        $dateRange = $this->getFilterValue('dateRange');

        // if daterange is present
        if ($this->isFilterExist('dateRange') && $dateRange) {
            $this->query->filterInRange($dateRange);
        }
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
