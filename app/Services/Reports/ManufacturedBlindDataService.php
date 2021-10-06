<?php

namespace App\Services\Reports;

use App\Models\Order;
use App\Models\ProcessSequence\ProcessSequence;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SuppCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            ->whereNotNull('orders.product_type')
            ->join('scanners AS sc', function ($join) use ($from, $to){
                $join->on('orders.serial_id', 'sc.blindid')
                    ->whereBetween('scannedtime', [$from, $to]);
            })
            ->groupBy('orders.serial_id')
            ->limit(10)
            ->get();

        $processSequences = ProcessSequence::with([
            'steps' => function ($query) {
                $query->with(['process']);
            }
        ])->get();

        $blinds = $this->sanitizeFullyProcessedBlinds($query, $processSequences);

        $blinds = $this->segregateByShift($blinds);

        return $blinds;
    }

    private function segregateByShift(): SuppCollection
    {

        return collect();
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

        //format Blinds
        $manufacturedBlindsData = $this->formatBlindsCollection($blindsCollection);

        return $manufacturedBlindsData;
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


        $data = $blindsCollection->filter(function ($blind, $blindKey) {
                    return $blind->scannedtime >= Carbon::parse($blind->scannedtime)
                        ->format('Y-m-d').' '. '06:00:00' &&
                        $blind->scannedtime <=  Carbon::parse($blind->scannedtime)
                        ->format('Y-m-d').' '. '14:00:00';
                })
                ->groupBy(
                    function ($date) {
                        return Carbon::parse($date->scannedtime)->format('Y-m-d');
                    }
                );

        return $data;
    }
}
