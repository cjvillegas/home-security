<?php

namespace App\Console\Commands\Cron;

use App\Abstracts\CronDatabasePopulator;
use App\Models\Order;
use App\Repositories\Orders\OrderRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PopulateOrdersFromBlindData extends CronDatabasePopulator
{
    /**
     * @var bool
     */
    private $checking;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:populate-orders-from-blind-data {--load-all} {--checking}';

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
        // initialize it here
        $this->checking = $this->option('checking');

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
                    // check if the CRON runs to check for missing orders
                    // this property relies on the switch coming from the command
                    if ($this->checking) {
                        $exists = Order::where('serial_id', $order->SerialID)->exists();

                        // if the order is already present in our production DB, ignore it.
                        if ($exists) {
                            continue;
                        }
                    }

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
        } catch (Exception $exception) {
            $name = $this->checking ? 'Populate Orders From Blind Data - Order Checking' : 'Populate Orders From Blind Data';
            $this->sendFailedNotification($name, $exception);

            // we need to log for tracking
            Log::info($name, [
                'checking' => $this->checking,
                'exception' => $exception
            ]);
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
        $factory = new OrderRepository();

        $where = "(o.order_id IS NOT NULL)
            AND (os.id <> '7')
            AND (bt.id <> '382')";

        // retrieve only from the most recent serial_id fetched + not load all + not order checking
        if ($latestSerialId && !$loadAll && !$this->checking) {
            $where .= "\t AND (sdl.id > {$latestSerialId})";
        }

        // if order checking
        if ($this->checking) {
            $where .= "\t AND o.dat_order >= DATEADD(day, -7, GETDATE())";
        }

        $query = $factory->generateBaseQueryForBlindData($where);

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
