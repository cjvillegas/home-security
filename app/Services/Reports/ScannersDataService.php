<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;
use App\Models\Export;
use App\Models\Scanner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ScannersDataService implements ServiceDataInterface
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
     * Retrieve scanners report data
     *
     * @param string $type
     *
     * @return Collection|LengthAwarePaginator|Builder
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
        }

        return $scanners;
    }

    /**
     * Return base query of this import
     *
     * @return self
     */
    public function buildQuery()
    {
        $query = Scanner::query()
            ->select([
                'orders.order_no',
                DB::raw('DATE_FORMAT(orders.ordered_at, "%Y-%m-%d") AS order_at'),
                'orders.customer',
                'orders.blind_type',
                'orders.quantity',
                'orders.serial_id',
                'processes.name',
                'employees.fullname',
                DB::raw('DATE_FORMAT(scanners.scannedtime, "%Y-%m-%d %H:%i") as scanned_date_time'),
            ])
            ->leftJoin('orders', 'orders.serial_id', '=', 'scanners.blindid')
            ->join('processes', 'processes.barcode', '=', 'scanners.processid')
            ->join('employees', 'employees.barcode', '=', 'scanners.employeeid');

        $this->query = $query;

        return $this;
    }

    function applyFilters()
    {
        $start = $this->getFilterValue('start');
        $end = $this->getFilterValue('end');

        if ($this->isFilterExist('start') && $this->isFilterExist('end')) {
            $this->query->whereBetween('scannedtime', [$start, $end]);
        }

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
     * Get the export type. The export type should have an Export counterpart.
     * Make sure you register a unique one in the Export model.
     *
     * @return string
     */
    public function exportType(): string
    {
        return Export::SCANNERS_RAW_DATA;
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
