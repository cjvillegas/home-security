<?php

namespace App\Services\Reports;

use App\Models\Export;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Main class that will export orders data it could either be on the front-end
 * or on a CSV report files
 *
 * @author Chaprel John Villegas
 */
class OrderDataService extends ReportDataService
{
    /**
     * OrderDataService constructor.
     */
    public function __construct(array $filters)
    {
        parent::__construct($filters);
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
                    $orders = $query->paginate($page, $size);
                } else {
                    $orders = $query->getResultInCollection();
                }

                break;
            case 'export':
                $this->query->limit(10000);
                $orders = $query->getResultInCollection()->map(function ($item) {
                    $item->scanners_id = $item->scanner ? $item->scanner->scanner_id : null;
                    $item->blindid = $item->scanner ? $item->scanner->blindid : null;
                    $item->last_updated_at = $item->scanner ? $item->scanner->last_updated_at : null;
                    $item->order_status = $item->scanner ? $item->scanner->order_status : null;

                    // remove the scanner relation
                    unset($item->scanner);

                    return $item;
                });

                break;
            case 'count';
                $orders = $this->query->count();

                break;
            case 'query';
                $orders = $this->query;

                break;
        }

        return $orders;
    }

    /**
     * Return base query of this import
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $query = Order::select([
                'orders.id',
                'orders.ordered_at',
                'orders.order_no',
                'orders.serial_id',
                'orders.blind_type',
                'orders.product_type',
                'orders.account_code',
                'orders.customer',
                'orders.customer_ref',
                DB::raw('1 AS qty'),
                'orders.width',
                'orders.drop',
                'orders.stock_code',
                'orders.fabric_range',
                'orders.color',
                'orders.item_price',
                'sa.work_date'
            ])
            ->with(['scanner' => function($query) {
                $employees = $this->getFilterValue('employees');
                $processes = $this->getFilterValue('processes');
                $query
                    ->select([
                        'scanners.id AS scanners_id',
                        'scanners.blindid',
                        'scanners.scannedtime AS last_updated_at',
                        'emp.fullname AS updated_by',
                        'pr.name AS order_status',
                        'pr.color AS color'
                    ])
                    ->leftJoin('employees AS emp', 'emp.barcode', 'scanners.employeeid')
                    ->leftJoin('processes AS pr', 'pr.barcode', 'scanners.processid')
                    ->when(!empty($employees) && is_array($employees), function ($query) use ($employees) {
                        $query->whereIn('emp.id', $employees);
                    })
                    ->when(!empty($processes) && is_array($processes), function ($query) use ($processes) {
                        $query->whereIn('pr.id', $processes);
                    })
                    ->orderBy('scanners.id', 'DESC');
            }])
            ->leftJoin('shift_assignments AS sa', 'sa.serial_id', 'orders.serial_id')
            ->orderBy('orders.order_no')
            ->groupBy('orders.id');

        $this->query = $query;

        return $this;
    }

    /**
     * Add query conditions based on the provided filters
     *
     * @return self
     */
    public function applyFilters(): self
    {
        $dates = $this->getFilterValue('dates');

        // filter between the ordered at date
        if (!empty($dates)) {
            $this->query->inBetweenDates($dates);
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
        return Export::ORDERS_EXPORT_REPORT;
    }
}
