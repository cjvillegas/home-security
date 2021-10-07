<?php

namespace App\Services\Reports;

use App\Models\Order;
use App\Models\ProcessSequence\ProcessSequence;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection as SuppCollection;
use Illuminate\Support\Facades\DB;

class ManufacturedBlindDataService
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
     * Retrieved all Blinds based on selected DateRange
     *
     * @return SuppCollection
     */
    public function getAllBlinds($dateRange): SuppCollection
    {
        $from = Carbon::parse($dateRange[0])->startOfDay()->format('Y-m-d H:i');
        $to = Carbon::parse($dateRange[1])->endOfDay()->format('Y-m-d H:i');

        $query = Order::query()
            ->select([
                'orders.id',
                'orders.order_no',
                'orders.product_type',
                'sc.scannedtime',
                'orders.serial_id',
                DB::raw('COUNT(DISTINCT sc.id) AS scanners_count')
            ])
            ->with(['latestScanner' => function($query) use ($from, $to) {
                $query->whereBetween('scannedtime', [$from, $to]);
            }])
            ->with('orderInvoice')
            ->whereNotNull('orders.product_type')
            ->join('scanners AS sc', function ($join) use ($from, $to){
                $join->on('orders.serial_id', 'sc.blindid')
                    ->whereBetween('scannedtime', [$from, $to]);
            })
            ->groupBy('orders.serial_id')
            ->get();

        $processSequences = ProcessSequence::with([
            'steps' => function ($query) {
                $query->with(['process']);
            }
        ])->get();

        $blinds = $this->sanitizeFullyProcessedBlinds($query, $processSequences);

        $blinds = $this->segregateByDaysAndShifts($blinds, $from, $to);

        $data = collect([
                    'blinds' => $blinds,
                    'totalManufacturedBlinds' => $this->totalManufacturedBlinds,
                    'totalInvoicedBlinds' => $this->totalInvoicedBlinds
                ]);
        return $data;
    }

    /**
     * To Seggregated the fetched data by Shifts
     *
     * @return SuppCollection
     */
    private function segregateByDaysAndShifts($blinds, $from, $to): SuppCollection
    {
        $data = collect();
        $period = CarbonPeriod::create($from, $to);
        foreach ($period as $date) {
        }
        // initialize Shifts
        $dates = $period->toArray();
        $shifts = [
            ['name' => 'Shift 1', 'start' => '06:00:00', 'end' => '14:00:00'],
            ['name' => 'Shift 2', 'start' => '14:00:00', 'end' => '22:00:00'],
            ['name' => 'Shift 3', 'start' => '22:00:00', 'end' => '06:00:00'],
        ];
        // Segregate per Date based on selected Date Range
        foreach ($dates as $date) {
            $dataPerDate = $blinds->where('scannedtime', '>=', Carbon::parse($date)->format('Y-m-d'). ' '. '00:00:00')
                ->where('scannedtime', '<=', Carbon::parse($date)->format('Y-m-d'). ' '. '23:59:59');

            // Sanitize if the data the specific date has Data
            if ($dataPerDate->count() > 0) {

                //Segregate data per Shifts
                foreach ($shifts as $shift) {
                    $start = Carbon::parse($date)->format('Y-m-d'). ' '. $shift['start'];
                    $end = Carbon::parse($date)->format('Y-m-d'). ' '. $shift['end'];

                    if ($shift['name'] == "Shift 3") {
                        $end = Carbon::parse($date)->addDay()->format('Y-m-d'). ' '. $shift['end'];
                    }
                    $manufacturedBlindsCount = $dataPerDate->where('scannedtime', '>=', $start)
                        ->where('scannedtime', '<=', $end)
                        ->count();
                    $invoicedBlindsCount =  $dataPerDate->where('scannedtime', '>=', $start)
                        ->where('scannedtime', '<=', $end)->whereNotNull('orderInvoice')
                        ->count();

                    $dateValue = Carbon::parse($date)->format('Y-m-d');
                    if ($shift['name'] == "Shift 3") {
                        $dateValue = Carbon::parse($date)->format('Y-m-d'). ' / '. Carbon::parse($date)->addDay()->format('Y-m-d');
                    }

                    //Increment overall total value
                    $this->totalManufacturedBlinds += $manufacturedBlindsCount;
                    $this->totalInvoicedBlinds += $invoicedBlindsCount;

                    $data->push([
                        'date' => $dateValue,
                        'shift' => $shift['name'],
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
     * Format Blinds Collection to count it per day and per shift
     *
     * @param  mixed $blinds
     *
     * @return SuppCollection
     */
    private function formatBlindsCollection($blindsCollection): SuppCollection
    {
        // $data = $blindsCollection->groupBy(function($date) {
        //             return Carbon::parse($date->scannedtime)->format('Y-m-d'); // grouping by years
        //         });




        return collect();
    }
}
