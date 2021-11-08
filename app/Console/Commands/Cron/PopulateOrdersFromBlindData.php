<?php

namespace App\Console\Commands\Cron;

use App\Abstracts\CronDatabasePopulator;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Collection;

class PopulateOrdersFromBlindData extends CronDatabasePopulator
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

        $this->table = 'orders';
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
            $orders = $this->getDataFromBlind($loadAll);

            $chunkCounter = 0;

            // chunk the results to save memory
            foreach ($orders->chunk(100) as $chunk) {
                // foreach to each instance of retrieved order
                $newOrders = [];
                foreach ($chunk as $order) {
                    // perform insertion of the order
                    $sanitized = $this->sanitize((array) $order);

                    // sanity check if sanitized data is not false
                    if ($sanitized) {
                        $newOrders[] = $sanitized;

                    }
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
        } catch (Exception $error) {
            $this->sendFailedNotification('Populate Orders From Blind Data', $error);
        }
    }

    /**
     * This will retrieve orders from SAGE
     *
     * @param bool $loadAll
     *
     * @return Collection
     */
    public function getDataFromBlind($loadAll = false): Collection
    {
        $latestSerialId = Order::getLatestSerialId();

        // initialize the query
        $query = "
            SELECT
                sdl.id AS SerialID,
                o.order_id AS OrderNo,
                o.dat_required AS RequiredDate,
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
                o.username AS OrderEnteredBy,
                u.sageaccount AS AccountCode,
                od.width_man as Width,
                od.drop_man as [Drop],
                f.code as StockCode,
                f.fabric_type as FabricRange,
                f.colour as Colour,
                od.nett_price as ItemPrice
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
            WHERE
                (o.order_id IS NOT NULL)
                AND (os.id <> '7')
                AND (bt.id <> '382')
        ";

        // if a blind_id present add additional condition to only load
        // data after this specified blind_id
        if ($latestSerialId && !$loadAll) {
            $query .= "\t AND (sdl.id > {$latestSerialId})";
        }

        // execute the query
        $orders = DB::connection('sqlsrv')->select($query);

        // return data as collection
        return collect($orders);
    }

    /**
     * Sanitize order item coming from SAGE
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $sageOrder
     *
     * @return array|null
     */
    protected function sanitize(array $sageOrder): ?array
    {
        // do a sanity check of the required data
        if (empty($sageOrder['SerialID']) ||
            empty($sageOrder['OrderNo']) ||
            empty($sageOrder['Customer']) ||
            empty($sageOrder['CustRef']) ||
            empty($sageOrder['ProductType']) ||
            empty($sageOrder['ProductCode'])) {

            return null;
        }

        $order['serial_id'] = $sageOrder['SerialID'];
        $order['order_no'] = $sageOrder['OrderNo'];
        $order['customer'] = $sageOrder['Customer'];
        $order['customer_ref'] = $sageOrder['CustRef'];
        $order['customer_order_no'] = $sageOrder['CustNo'];
        $order['product_type'] = $sageOrder['ProductType'];
        $order['blind_type'] = $sageOrder['ProductCode'];
        $order['ordered_at'] = $sageOrder['Ordered'];
        $order['required_date'] = $sageOrder['RequiredDate'];
        $order['order_entered_by'] = $sageOrder['OrderEnteredBy'];
        $order['account_code'] = $sageOrder['AccountCode'];
        $order['width'] = $sageOrder['Width'];
        $order['drop'] = $sageOrder['Drop'];
        $order['stock_code'] = $sageOrder['StockCode'];
        $order['fabric_range'] = $sageOrder['FabricRange'];
        $order['color'] = $sageOrder['Colour'];
        $order['item_price'] = $sageOrder['ItemPrice'];
        $order['created_at'] = now('UTC')->format('Y-m-d H:i:s');

        return $order;
    }
}
