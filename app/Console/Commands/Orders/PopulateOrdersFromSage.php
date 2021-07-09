<?php

namespace App\Console\Commands\Orders;

use App\Models\Order;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Log;
use \Illuminate\Support\Collection;

class PopulateOrdersFromSage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:populate-orders-from-sage {--load-all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will populate the data of the orders table from SAGE. Note* This method will clear the orders table first then re-populate it with the fetched data from SAGE';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('CRON for populating orders table from SAGE is RUNNING!!!');

        $loadAll = $this->option('load-all');

        // if to load all data, truncate the orders table
        if ($loadAll) {
            $this->clearTable();
        }

        // retrieve orders from sage
        $orders = $this->getOrdersData($loadAll);

        $chunkCounter = 0;

        // chunk the results to save memory
        foreach ($orders->chunk(100) as $chunk) {
            // foreach to each instance of retrieved order
            $newOrders = [];
            foreach ($chunk as $order) {
                // perform insertion of the order
                $newOrders[] = $this->sanitize((array) $order);
            }

            // do the actual insertion of data
            Order::insert($newOrders);

            // increment the execution counter
            $chunkCounter++;

            // execution throttling
            // make the server rest after 10 bulk insert
            if ($chunkCounter % 10 === 0) {
                usleep(100000);
            }
        }

        return 0;
    }

    /**
     * This will retrieve orders from SAGE
     *
     * @param bool $loadAll
     *
     * @return Collection
     */
    public function getOrdersData($loadAll = false): Collection
    {
        $latestBlindId = Order::getLatestBlindId();

        // initialize the query
        $query = "
            SELECT
                OrderDetail.id AS BlindId,
                [Order].order_id AS OrderNo,
                [User].company AS Customer,
                [Order].cust_no AS CustOrdNo,
                OrderDetail.quantity AS Quantity,
                BlindType.description AS BlindType,
                DetailStatus.name AS BlindStatus,
                [Order].dat_delivery AS DespatchDate,
                [Order].dat_order AS Ordered,
                [Order].username AS OrderEnteredBy,
                SerialDetailLine.id as SerialID
            FROM
                OrderDetail
                INNER JOIN [Order] ON OrderDetail.order_id = [Order].id
                INNER JOIN [User] ON [Order].user_id = [User].id
                INNER JOIN BlindType ON OrderDetail.blindtype_id = BlindType.id
                INNER JOIN Fabric ON OrderDetail.fabric_id = Fabric.id
                INNER JOIN OrderStatus ON [Order].orderstatus_id = OrderStatus.id
                INNER JOIN DetailStatus ON OrderDetail.detailstatus_id = DetailStatus.id
                INNER JOIN ManLocation ON BlindType.manlocation_id = ManLocation.id
                INNER JOIN SerialDetailLine ON OrderDetail.id = SerialDetailLine.OrderDetail_id
                LEFT OUTER JOIN RollerTable ON OrderDetail.RollerTableID = RollerTable.ID
            WHERE
                ([Order].order_id IS NOT NULL)
                AND (OrderStatus.IsOrder = '1')
                AND (OrderStatus.IsQuotation = '0')
                AND (OrderStatus.id NOT LIKE '7')
                AND (BlindType.id <> '382')
        ";

        // if a blind_id present add additional condition to only load
        // data after this specified blind_id
        if ($latestBlindId && !$loadAll) {
            $query .= "\t AND (OrderDetail.id > {$latestBlindId})";
        }

        // execute the query
        $orders = DB::connection('sqlsrv')->select($query);

        // return data as collection
        return collect($orders);
    }

    /**
     * This will clear the data from the **orders** table.
     * This method will really do truncation on the orders table, not soft deletion.
     *
     * @return void
     */
    private function clearTable(): void
    {
        Order::truncate();
    }


    /**
     * Sanitize order item coming from SAGE
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $sageOrder
     *
     * @return mixed
     */
    private function sanitize(array $sageOrder)
    {
        // do a sanity check of the required data
        if (empty($sageOrder['BlindId']) || empty($sageOrder['OrderNo']) || empty($sageOrder['Customer']) || empty($sageOrder['Quantity']) || empty($sageOrder['OrderEnteredBy']) || empty($sageOrder['SerialID'])) {
            return false;
        }

        $order['blind_id'] = $sageOrder['BlindId'];
        $order['order_no'] = $sageOrder['OrderNo'];
        $order['customer'] = $sageOrder['Customer'];
        $order['customer_order_no'] = $sageOrder['CustOrdNo'];
        $order['quantity'] = $sageOrder['Quantity'];
        $order['blind_type'] = $sageOrder['BlindType'] ?? '';
        $order['blind_status'] = $sageOrder['BlindStatus'] ?? '';
        $order['order_entered_by'] = $sageOrder['OrderEnteredBy'];
        $order['despatched_at'] = !empty($sageOrder['DespatchDate']) ? Carbon::parse($sageOrder['DespatchDate'])->format('Y-m-d H:i:s') : null;
        $order['ordered_at'] = !empty($sageOrder['Ordered']) ? Carbon::parse($sageOrder['Ordered'])->format('Y-m-d H:i:s') : null;
        $order['serial_id'] = $sageOrder['SerialID'];
        $order['created_at'] = now('UTC')->format('Y-m-d H:i:s');

        return $order;
    }
}
