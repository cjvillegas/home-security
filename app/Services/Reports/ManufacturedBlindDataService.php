<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;
use App\Models\Order;
use App\Models\ProcessSequence\ProcessSequence;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * @return Collection
     */
    public function getAllBlinds($dateRange): Collection
    {
        $from = $dateRange[0];
        $to = $dateRange[1];

        $query = Order::query()
            ->select([
                'orders.id',
                'orders.order_no',
                'orders.product_type',
                'sc.scannedtime',
                'orders.serial_id'
            ])
            ->with(['scanners', 'processSequence' => function($query) {
                $query->with(['steps' => function($query) {
                    $query->with('process');
                }]);
            }])
            ->whereBetween('scannedtime', [$from, $to])
            ->whereNotNull('orders.product_type')
            ->leftJoin('scanners AS sc', 'orders.serial_id', 'sc.blindid')
            ->groupBy('orders.serial_id')
            ->get();

        $blinds = $this->sanitizeFullyProcessedBlinds($query);

        return $blinds;
    }

    /**
     * This will return the Sanitized Fully Processed Blinds.
     *
     * @param  mixed $blinds
     *
     * @return bool
     */
    function sanitizeFullyProcessedBlinds($blinds): Collection
    {
        $blindsCollection = new Collection();

        $isFullyProcessed = false;
        foreach ($blinds as $blind) {
            $isFullyProcessed = $this->isFullyProcessed($blind);
            if ($isFullyProcessed) {
                $blindsCollection->push($blind);
            }
        }
        $blindsCollection = $blindsCollection->filter(
            function ($blind, $blindKey) {
                return $blind->scannedtime > Carbon::parse($blind->scannedtime)
                    ->format('Y-m-d').' '. '18:00:00';
            }
        )
        ->groupBy(
            function ($date) {
                return Carbon::parse($date->scannedtime)->format('Y-m-d');
            }
        );
        return $blindsCollection;
    }

    /**
     * Determine whether the specific blind is Fully processed or not.
     *
     * @param  mixed $blind
     *
     * @return Boolean
     */
    function isFullyProcessed($blind): bool
    {
        dd($blind);
        $isFullyProcessed = false;
        if ($blind->process_sequence) {
            $isFullyProcessed = $processSequence->steps->every(function($value, $index) use ($blind) {
                $process = $value->process;
                if ($process) {
                    return $blind->scanners->contains(function($scanner, $scannerIndex) use ($process) {
                        return $scanner->processid == $process->barcode;
                    });
                }
                return false;
            });
        }

        return $isFullyProcessed;
    }
}
