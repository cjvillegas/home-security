<?php

namespace App\Services\Reports;

use App\Models\Machine;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardMachineStatisticsDataService extends ReportDataService
{
    public function getData(string $type)
    {
        $query = $this->buildQuery()->applyFilters();

        switch ($type) {
            case 'list':
                $scanners = $query->getResultInCollection();

                break;
        }

        return $scanners;
    }

    /**
     * Base query of this service class
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $query = Order::query()
            ->select([
                DB::raw("(CASE WHEN oi.id IS NULL THEN 1 ELSE 0 END) AS processed_blinds"),
                DB::raw("(DISTINCT CASE WHEN oi.id IS NOT NULL THEN 1 ELSE 0 END) AS completed_orders")
            ])
            ->with()
            ->join('scanners AS sc', 'sc.blindid', 'orders.serial_id')
            ->join('machines', 'machines.id', 'sc.machineid')
            ->leftJoin('order_invoices AS oi', 'oi.order_no', 'orders.order_no')
            ->groupBy('orders.order_no');

        $this->query = $query;

        return $this;
    }

    /**
     * @return self
     */
    public function applyFilters(): self
    {
        $machineIds = $this->getMachineIdsByName();

        if ($this->isFilterExist('dates') && is_array($dates = $this->getFilterValue('dates'))) {
            $this->query->filterInBetweenDates($dates);
        }

        if (!!$machineIds) {
            $this->query->whereIn('scanners.machineid', $machineIds);
        }

        return $this;
    }

    /**
     * Fetch and return machine ids based on the given names
     *
     * @return array
     */
    private function getMachineIdsByName(): array
    {
        return Machine::whereIn('name', $this->getFilterValue('machines'))
            ->get()
            ->pluck('id')
            ->toArray();
    }
}
