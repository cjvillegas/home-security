<?php

namespace App\Services\Reports;

use App\Models\Machine;
use App\Models\Order;
use App\Models\Scanner;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardMachineStatisticsDataService extends ReportDataService
{
    /**
     * Collection of machines
     *
     * @var Collection
     */
    private $machines;

    /**
     * DashboardMachineStatisticsDataService constructor.
     */
    public function __construct(array $filters)
    {
        parent::__construct($filters);

        $this->machines = $this->getMachinesByName();
    }

    /**
     * @param string $type
     *
     * @return Collection|mixed
     */
    public function getData(string $type)
    {
        $query = $this->buildQuery()->applyFilters();

        switch ($type) {
            case 'list':
                $orders = $this->segregateBlindItems($query->getResultInCollection());
//                dumpSql();

                break;
        }

        return $orders;
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
                'orders.id',
                'orders.order_no',
                'orders.serial_id',
                'oi.id AS invoice_id',
                'oi.date AS invoiced_at'
            ])
            ->with(['scanners' => function  ($relation) {
                $relation->select([
                    'scanners.id',
                    'scanners.blindid',
                    'scanners.machineid',
                    'scanners.scannedtime',
                    'mc.name AS machine_name'
                ])
                ->filterInBetweenDates($this->getFilterValue('dates'))
                ->join('machines AS mc', 'mc.id', 'scanners.machineid')
                ->whereNotNull('scanners.machineid');
            }])
            ->join('scanners AS sc', 'sc.blindid', 'orders.serial_id')
            ->join('machines', 'machines.id', 'sc.machineid')
            ->leftJoin('order_invoices AS oi', 'oi.order_no', 'orders.order_no')
            ->whereNotNull('sc.machineid')
            ->groupBy('orders.id');

        $this->query = $query;

        return $this;
    }

    /**
     * Apply filter conditions
     *
     * @return self
     */
    public function applyFilters(): self
    {
        $machineIds = $this->machines->pluck('id')->toArray();

        if ($this->isFilterExist('dates') && is_array($dates = $this->getFilterValue('dates'))) {
            $dates = Shift::getShiftsStartAndEndBased($dates[0], $dates[1]);
            $this->query->whereBetween('sc.scannedtime', $dates);
        }

        if (!!$machineIds) {
            $this->query->whereIn('sc.machineid', $machineIds);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    private function segregateBlindItems(Collection $blinds): Collection
    {
        $type = $this->getFilterValue('type');
        $shifts = Shift::SHIFT_TIME_LIST;

        // get filter dates in Y-m-d format
        $dates = $this->getFilterDates();

        // define an empty data set
        $dataSet = [
            'yesterday' => [],
            'today' => []
        ];

        $currentShift = null;
        foreach ($shifts as $shiftKey => $shift) {
            // generate an empty shift data set
            if ($currentShift !== $shiftKey) {
                $this->generateShiftEmptySet($dataSet, $shiftKey);
                $currentShift = $shiftKey;
            }

            // track the invoiced orders
            $trackedOrderNums = [];

            foreach ($blinds as $blind) {
                // if this blind's order has been invoiced and this blind's order is not been counted yet
                // then add this to the tracked invoiced orders. This will prevent us to count orders twice
                $countCompleted = false;
                if ($type === 'vertical') {
                    if ($blind->invoice_id && !in_array($blind->order_no, $trackedOrderNums)) {
                        $trackedOrderNums[] = $blind->order_no;
                        $countCompleted = true;
                    }
                }

                $baseKey = 'today';
                $today = $this->getStartAndEndDate($shiftKey, $shift, $dates[$baseKey]);

                $scanner = $this->getScanner($blind->scanners, $today);

                // if the scanner is not today, then check if it is from yesterday
                if (empty($scanner)) {
                    $baseKey = 'yesterday';
                    $yesterday = $this->getStartAndEndDate($shiftKey, $shift, $dates[$baseKey]);

                    $scanner = $this->getScanner($blind->scanners, $yesterday);
                }

                // if no scanner found in this particular shift range, just neglect
                if (empty($scanner)) {
                    continue;
                }

                // if this blind's order has been invoiced and this blind's order is not been counted yet
                // then add this to the tracked invoiced orders. This will prevent us to count orders twice
                $countProcessed = false;
                if ($type === 'venetian') {
                    if (!in_array($blind->order_no, $trackedOrderNums)) {
                        $trackedOrderNums[] = $blind->order_no;
                        $countProcessed = true;
                    }
                }

                // set empty set of data set
                if (!isset($dataSet[$baseKey][$shiftKey][$scanner->machineid])) {
                    $dataSet[$baseKey][$shiftKey][$scanner->machineid] = [
                        'processed_blinds' => 0,
                        'completed_orders' => 0,
                        'headrail_cut' => 0,
                        'processed_orders' => 0,
                        'machine_name' => $scanner->machine_name,
                    ];
                }

                $dataSet[$baseKey][$shiftKey][$scanner->machineid]['processed_blinds']++;
                $dataSet[$baseKey][$shiftKey][$scanner->machineid]['completed_orders'] += $countCompleted;
                $dataSet[$baseKey][$shiftKey][$scanner->machineid]['processed_orders'] += $countProcessed;

                // set empty set for total data set
                if (!isset($dataSet[$baseKey][$shiftKey]['total'])) {
                    $dataSet[$baseKey][$shiftKey]['total'] = [
                        'processed_blinds' => 0,
                        'completed_orders' => 0,
                        'headrail_cut' => 0,
                        'processed_orders' => 0,
                    ];
                }

                $dataSet[$baseKey][$shiftKey]['total']['processed_blinds']++;
                $dataSet[$baseKey][$shiftKey]['total']['completed_orders'] += $countCompleted;
                $dataSet[$baseKey][$shiftKey]['total']['processed_orders'] += $countProcessed;

                // increment the headrail cut
                if (in_array($scanner->machine_name, ['GL632', 'GL70'])) {
                    $dataSet[$baseKey][$shiftKey][$scanner->machineid]['headrail_cut']++;
                    $dataSet[$baseKey][$shiftKey]['total']['headrail_cut']++;
                }
            }
        }

        return collect($dataSet);
    }

    /**
     * @return void
     */
    private function generateShiftEmptySet(array &$dataSet, $shiftKey): void
    {
        // for today
        $dataSet['today'][$shiftKey] = [];
        $dataSet['today'][$shiftKey]['name'] = ucwords(str_replace('_', ' ', $shiftKey));

        // for yesterday
        $dataSet['yesterday'][$shiftKey] = [];
        $dataSet['yesterday'][$shiftKey]['name'] = ucwords(str_replace('_', ' ', $shiftKey));
    }

    /**
     * @param Collection $scanners
     * @param array $dates
     *
     * @return Scanner|null
     */
    private function getScanner(Collection $scanners, array $dates): ?Scanner
    {
        return $scanners->first(function ($item) use ($dates) {
            return Carbon::parse($item->scannedtime)->between($dates[0], $dates[1]);
        });
    }

    /**
     * Get the start of end date of a shift
     *
     * @param string $shift
     * @param array $timeRange
     * @param string $timeRange
     *
     * @return array
     */
    private function getStartAndEndDate(string $shift = null, array $timeRange, string $baseDate): array
    {
        $shiftStart = "{$baseDate} {$timeRange[0]}";
        $shiftEnd = "{$baseDate} {$timeRange[1]}";

        // if it is shift three, make sure we add a day to move the day pointer to the next day
        if ($shift === 'shift_three') {
            $shiftEnd = Carbon::parse($shiftEnd)->addDay()->format('Y-m-d H:i:s');
        }

        return [
            $shiftStart,
            $shiftEnd
        ];
    }

    /**
     * Get the filter dates in Y-m-d format
     *
     * @return array
     */
    private function getFilterDates(): array
    {
        $dates = $this->getFilterValue('dates');

        return [
            'yesterday' => Carbon::parse($dates[0])->format('Y-m-d'),
            'today' => Carbon::parse($dates[1])->format('Y-m-d'),
        ];
    }

    /**
     * Return machines based on the given list of names
     *
     * @return Collection
     */
    private function getMachinesByName(): Collection
    {
        return Machine::whereIn('name', $this->getFilterValue('machines'))
            ->get();
    }
}
