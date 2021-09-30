<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProcessSequence\ProcessSequence;
use App\Services\Reports\ManufacturedBlindDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;

class ManufacturedBlindController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reports.manufactured-blinds');
    }

    /**
     * To determine if Order is Fully Processed
     *
     * @return bool
     */
    function isFullyProcessed($order): bool
    {
        $processSequences = ProcessSequence::select([
                'process_sequences.*',
                'o.id AS order_id',
                'o.order_no AS order_no',
            ])
            ->with(['steps' => function ($query) {
                $query->with('process');
            }])
            ->join('orders AS o', 'o.product_type', 'process_sequences.name')
            ->where('o.order_no', $order->order_no)
            ->groupBy('process_sequences.id')
            ->get();

        Log::info($order->order_no);
        foreach ( $order->scanners as $scanner ) {
            Log::info($scanner);
        }

        Log::info($processSequences); die();

        return true;
    }

    function ordersInProcess($order)
    {
        $order->scanners->count();

        return $order;
    }
    /**
     * Fetch all Manufactured Blinds based on Date selected
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function getBlinds(Request $request)
    {
        $from = $request->dateRange[0];
        $to = $request->dateRange[1];
        //$service = new ManufacturedBlindDataService($request->all());

        $blinds = Order::query()
        ->select([
            'orders.id',
            'orders.order_no',
            'orders.product_type',
            'sc.scannedtime',
            'orders.serial_id'
        ])
        ->with('scanners')
        ->whereBetween('scannedtime', [$from, $to])
        ->whereNotNull('orders.product_type')
        ->leftJoin('scanners AS sc', 'orders.serial_id', 'sc.blindid')
        ->groupBy('orders.serial_id')
        ->get();

        $formmatedBlinds = array();
        foreach ($blinds as $blind) {

            $isFullyProcessed = $this->isFullyProcessed($blind);

            $blindObject = new stdClass();
            $blindObject->formatted_id = $blind['id'];
            $formmatedBlinds[] = array("formatted_id" => $blind['id']);
        }

        Log::info($formmatedBlinds);

        die();
        return response()->json(['blinds' => $blinds]);
    }

    /**
     * Export Manufactured Blinds Data
     *
     * @param  mixed $request
     *
     * @return null
     */
    public function exportManufacturedBlinds(Request $request)
    {
        return null;
    }
}
