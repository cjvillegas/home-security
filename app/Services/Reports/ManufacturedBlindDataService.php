<?php

namespace App\Services\Reports;

use App\Models\Export;
use App\Models\Scanner;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection as SuppCollection;

class ManufacturedBlindDataService extends ReportDataService
{
    /**
     * @var mixed
     */
    private $processSequences;

    /**
     * @var mixed
     */
    private $dateRange;

    /**
     * Overall total Blinds from selected Date Range
     *
     * @var int
     */
    private $totalBlinds =0;

    /**
     * Overall total Manufactured Blinds
     *
     * @var int
     */
    private $totalManufacturedBlinds = 0;

    /**
     * Overall total Invoiced Blinds
     *
     * @var int
     */
    private $totalInvoicedBlinds = 0;

    /**
     * QualityControlFaultDataService constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters)
    {
        parent::__construct($filters);
    }

    /**
     * Retrieved all Blinds based on selected DateRange
     *
     * @return SuppCollection
     */
    public function getData(string $type): SuppCollection
    {
        $data = collect();
        $query = $this->buildQuery();

        switch ($type) {
            case 'list':
                $blinds = collect($this->query->get());

                $this->totalBlinds = $blinds->count();
                $blinds = $this->segregateByDaysAndShifts($blinds, $this->dateRange['from'], $this->dateRange['to']);

                $data = collect([
                            'blinds' => $blinds,
                            'totalBlinds' => $this->totalBlinds,
                            'totalManufacturedBlinds' => $this->totalManufacturedBlinds,
                            'totalInvoicedBlinds' => $this->totalInvoicedBlinds
                        ]);

                break;
            case 'export':
                $blinds = collect($this->query->get());

                $blinds = $this->segregateByDaysAndShifts($blinds, $this->dateRange['from'], $this->dateRange['to']);

                //append the OVERALL total value on Last row
                $blinds = $blinds->push([
                            'date' => 'TOTAL VALUE: ',
                            'shift' => '-',
                            'manufactured_blinds' => $this->totalManufacturedBlinds,
                            'invoiced_blinds' => $this->totalInvoicedBlinds
                        ]);

                $data = $blinds;
                break;

        }

        return $data;
    }

    /**
     * Get the base query for this report
     *
     * @return self
     */
    public function buildQuery(): self
    {
        // assign dateRange filters to private variables
        $this->dateRange['from'] = Carbon::parse($this->getFilterValue('start'))->format('Y-m-d'). ' ' . '06:00:00';
        $this->dateRange['to'] = Carbon::parse($this->getFilterValue('end'))->format('Y-m-d'). ' ' . '05:59:29';
        $from = $this->dateRange['from'];
        $to = $this->dateRange['to'];
        //new logic - using scanner and processid
        // these are the processid that will determine if the Order/Blind is Fully PRocessed
        $processes = ['P1025', 'P1024', 'P1021', 'P1002', 'P5688737'];

        $query = Scanner::query()
            ->select(['id','scannedtime', 'blindid', 'processid'])
            ->with('order.orderInvoice')
            ->with('orderInvoice')
            ->byProcesses($processes)
            ->whereBetween('scannedtime', [$from, $to])
            ->groupBy('blindid');

        $this->query = $query;

        return $this;
    }

    /**
     * To Seggregated the fetched data by Shifts
     *
     * @return SuppCollection
     */
    private function segregateByDaysAndShifts($scanners, $from, $to): SuppCollection
    {
        $data = collect();
        $period = CarbonPeriod::create($from, $to);
        foreach ($period as $date) {
        }
        // initialize Shifts
        $dates = $period->toArray();
        $shifts = array(Shift::SHIFT_ONE_TIME, Shift::SHIFT_TWO_TIME, Shift::SHIFT_THREE_TIME);

        // Segregate per Date based on selected Date Range
        foreach ($dates as $date) {
            $scannersPerDate = $scanners->where('scannedtime', '>=', Carbon::parse($date)->format('Y-m-d'). ' '. '06:00:00')
                ->where('scannedtime', '<=', Carbon::parse($date)->addDay()->format('Y-m-d'). ' '. '05:59:59');

            // Sanitize if the data the specific date has Data
            if ($scannersPerDate->count() > 0) {

                //Segregate data per Shifts
                foreach ($shifts as $key=>$shift) {
                    $start = Carbon::parse($date)->format('Y-m-d'). ' '. $shift[0];
                    $end = Carbon::parse($date)->format('Y-m-d'). ' '. $shift[1];

                    // if shift 3
                    if ($key == 2) {
                        $end = Carbon::parse($date)->addDay()->format('Y-m-d'). ' '. $shift[1];
                    }
                    $manufacturedBlindsCount = $scannersPerDate->where('scannedtime', '>=', $start)
                        ->where('scannedtime', '<=', $end)
                        ->count();

                    $invoicedBlindsCount =  $scannersPerDate->where('scannedtime', '>=', $start)
                        ->where('scannedtime', '<=', $end)->whereNotNull('order.orderInvoice')
                        ->count();

                    $dateValue = Carbon::parse($date)->format('Y-m-d');

                    // if shift 3
                    if ($key == 2) {
                        $dateValue = Carbon::parse($date)->format('Y-m-d'). ' / '. Carbon::parse($date)->addDay()->format('Y-m-d');
                    }

                    //Increment overall total value
                    $this->totalManufacturedBlinds += $manufacturedBlindsCount;
                    $this->totalInvoicedBlinds += $invoicedBlindsCount;

                    $data->push([
                        'date' => $dateValue,
                        'shift' => 'Shift '. ($key + 1),
                        'manufactured_blinds' => $manufacturedBlindsCount,
                        'invoiced_blinds' => $invoicedBlindsCount
                    ]);
                }
            }
        }

        return $data;
    }

    /**
     * This will return the Sanitized Fully Processed Blinds.
     *
     * @param  mixed $blinds
     * @param  SuppCollection $processSequences
     *
     * @return SuppCollection
     */
    public function sanitizeFullyProcessedBlinds($blinds, SuppCollection $processSequences): SuppCollection
    {
        $blindsCollection = collect();

        foreach ($blinds as $blind) {
            $sequence = $processSequences->first(function ($item) use ($blind) {
                return strtolower($item->name) === strtolower($blind->product_type);
            });

            // sanity check: blind should have a valid sequence
            if (empty($sequence)) {
                continue;
            }

            // if the number of steps is equal to blind's scanners, its fully processed
            $isFullyProcessed = $sequence->steps->count() === $blind->scanners_count;

            if ($isFullyProcessed) {
                $blindsCollection->push($blind);
            }
        }

        return $blindsCollection;
    }

    /**
     * Apply the present filters to the query.
     *
     * @return self
     */
    public function applyFilters(): self
    {
        $dateRange = $this->getFilterValue('dateRange');

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
        return Export::MANUFACTURED_BLIND_REPORT;
    }
}
