<?php

namespace App\Console\Commands\Cron;

use App\Models\Order;
use App\Models\User;
use App\Notifications\CronFailureNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class PopulateOrdersFromBlindData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:populate-orders-from-blind-data {--load-all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will populate the data of the orders table from BlindData. Note* This method will clear the orders table first if load-all flag is true then re-populate it with the fetched data from SAGE';

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
     * @return void
     */
    public function handle(): void
    {
        try {
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
        } catch (Exception $err) {
            $users = (new User)->getUserAdminsWithValidEmails();

            Notification::send($users, new CronFailureNotification('Populate Orders From Blind Data', $err->getMessage()));
        }
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
        $latestSerialId = Order::getLatestSerialId();

        $newQuery = "
            SELECT
                sdl.id AS SerialID,
                o.order_id AS OrderNo,
                u.company AS Customer,
                o.cust_ref AS CustRef,
                o.cust_no AS CustNo,
                CASE WHEN ml.location = 'Aluminium' THEN 'Aluminium'
                WHEN ml.location LIKE '%Vertical%' AND f.fabric_type NOT LIKE '%Headrail%' AND bt.description NOT LIKE '%Louvers%' AND bt.code NOT LIKE '%RIGID%' THEN 'Vertical'
                WHEN ml.location LIKE '%Vertical%' AND f.fabric_type NOT LIKE '%Headrail%' AND bt.description LIKE '%Louvers%' AND bt.code NOT LIKE 'LO89-RIGID-PVC' THEN 'LouversOnly'
                WHEN ml.location LIKE '%Vertical%' AND f.fabric_type LIKE '%Headrail%' AND bt.description NOT LIKE '%Louvers%' THEN 'HeadrailOnly'
                WHEN ml.location LIKE '%Vertical%' AND f.fabric_type NOT LIKE '%Headrail%'AND bt.code LIKE '%RIGID%' AND bt.description NOT LIKE '%Louvers%' THEN 'VerticalRigidPVC'
                WHEN ml.location LIKE '%Vertical%' AND f.fabric_type NOT LIKE '%Headrail%' AND bt.code LIKE '%RIGID%' AND bt.description LIKE '%Louvers%' THEN 'LouversOnlyRigidPVC'
                WHEN ml.location = 'Roller Express' AND o.order_id NOT IN (SELECT o.order_id FROM [Order] o INNER JOIN OrderDetail od ON o.id = od.order_id INNER JOIN Fabric f ON od.fabric_id = f.id INNER JOIN BlindType bt ON od.blindtype_id = bt.id WHERE (f.code LIKE '%AP' OR f.code LIKE '%AV' OR f.code LIKE 'NTW%') AND bt.code = 'ROLLEXP' AND o.order_id IS NOT NULL) THEN 'RollerExpress'
                WHEN (NOT(od.option_list LIKE '%Motor%' AND od.option_list NOT LIKE '%No Motor%') AND (ml.location = 'Rollers' OR ml.location = 'RDRS') AND o.order_id IS NOT NULL) OR ((ml.location = 'Rollers' OR ml.location = 'RDRS') AND od.option_list NOT LIKE '%Motor%' AND o.order_id IS NOT NULL) OR (bt.code = 'ROLLEXP' AND o.order_id IN (SELECT o.order_id FROM [Order] o INNER JOIN OrderDetail od ON o.id = od.order_id INNER JOIN Fabric f ON od.fabric_id = f.id INNER JOIN BlindType bt ON od.blindtype_id = bt.id WHERE (f.code LIKE '%AP' OR f.code LIKE '%AV' OR f.code LIKE 'NTW%') AND bt.code = 'ROLLEXP' AND o.order_id IS NOT NULL) AND o.order_id IS NOT NULL) THEN 'Roller'
                WHEN ml.location = 'Contracts Department' AND od.option_list LIKE '%Chain%' THEN 'TechnicalChained'
                WHEN ml.location = 'Contracts Department' AND od.option_list LIKE '%Crank%' THEN 'TechnicalCrank'
                WHEN (o.order_id IS NOT NULL AND ml.location = 'Contracts Department' AND od.option_list LIKE '%Motor%') OR (o.order_id IS NOT NULL AND (od.option_list LIKE '%Motor%' AND od.option_list NOT LIKE '%No Motor%') AND (ml.location = 'Rollers' OR ml.location = 'RDRS')) THEN 'TechnicalMotorised'
                END AS ProductType,
                bt.code AS ProductCode,
                o.dat_order AS Ordered,
                o.username AS OrderEnteredBy
            FROM
                OrderDetail od
                INNER JOIN [Order] o ON od.order_id = o.id
                INNER JOIN [User] u ON o.user_id = u.id
                INNER JOIN BlindType bt ON od.blindtype_id = bt.id
                INNER JOIN Fabric f ON od.fabric_id = f.id
                INNER JOIN OrderStatus os ON o.orderstatus_id = os.id
                INNER JOIN DetailStatus ds ON od.detailstatus_id = ds.id
                INNER JOIN ManLocation ml ON bt.manlocation_id = ml.id
                INNER JOIN SerialDetailLine sdl ON od.id = sdl.OrderDetail_id
                INNER JOIN [Category] c ON bt.category_id = c.id
                LEFT OUTER JOIN RollerTable rt ON od.RollerTableID = rt.ID
            WHERE
                (o.order_id IS NOT NULL)
                AND (os.id <> '7')
                AND (bt.id <> '382')
        ";

        // initialize the query
        $query = "
            SELECT
                TOP 1000
                OrderDetail.id AS BlindId,
                [Order].order_id AS OrderNo,
                [User].company AS Customer,
                [Order].cust_no AS CustOrdNo,
                OrderDetail.quantity AS Quantity,
                BlindType.code AS BlindCode,
                DetailStatus.name AS BlindStatus,
                [Order].dat_delivery AS DespatchDate,
                [Order].dat_order AS Ordered,
                [Order].username AS OrderEnteredBy,
                SerialDetailLine.id AS SerialID,
                [Category].name AS CategoryName,
                [Category].id AS CategoryID
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
                INNER JOIN [Category] ON BlindType.category_id = [Category].id
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
        if ($latestSerialId && !$loadAll) {
            $query .= "\t AND (SerialDetailLine.id > {$latestSerialId})";
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
        $model = new Order();
        $model->setConnection('mysql')
            ->truncate();
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
        $order['blind_type'] = $sageOrder['BlindCode'] ?? '';
        $order['blind_status'] = $sageOrder['BlindStatus'] ?? '';
        $order['order_entered_by'] = $sageOrder['OrderEnteredBy'];
        $order['despatched_at'] = !empty($sageOrder['DespatchDate']) ? Carbon::parse($sageOrder['DespatchDate'])->format('Y-m-d H:i:s') : null;
        $order['ordered_at'] = !empty($sageOrder['Ordered']) ? Carbon::parse($sageOrder['Ordered'])->format('Y-m-d H:i:s') : null;
        $order['category_id'] = $sageOrder['CategoryID'] ?? null;
        $order['category_name'] = $sageOrder['CategoryName'] ?? '';
        $order['serial_id'] = $sageOrder['SerialID'];
        $order['created_at'] = now('UTC')->format('Y-m-d H:i:s');

        return $order;
    }
}
