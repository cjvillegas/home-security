<?php

namespace App\Services\Reports;

use App\Models\Order;
use App\Models\ProcessSequence\ProcessSequence;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
            ->whereNotNull('orders.product_type')
            ->leftJoin('scanners AS sc', function ($join) use ($from, $to){
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

        return $blinds;
    }

    /**
     * This will return the Sanitized Fully Processed Blinds.
     *
     * @param  mixed $blinds
     * @param  SuppCollection $processSequences
     *
     * @return SuppCollection
     */
    function sanitizeFullyProcessedBlinds($blinds, SuppCollection $processSequences): SuppCollection
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

            $isFullyProcessed = $sequence->steps->count() === $blind->scanners_count;

            if ($isFullyProcessed) {
                $blindsCollection->push($blind);
            }
        }

        return $blindsCollection;
    }
}
