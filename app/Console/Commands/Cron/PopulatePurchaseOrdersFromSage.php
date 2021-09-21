<?php

namespace App\Console\Commands\Cron;

use App\Abstracts\CronDatabasePopulator;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class PopulatePurchaseOrdersFromSage extends CronDatabasePopulator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:populate-purchase-orders-from-sage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will populate Purchase Orders From Sage Server';

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
        try {
            $purchaseOrders = $this->getDataFromBlind();

            $chunkCounter = 0;
            // chunk the results to save memory
            // chunk the results to save memory
            foreach ($purchaseOrders->chunk(100) as $chunk) {
                // foreach to each instance of retrieved order
                $newPurchaseOrders = [];
                foreach ($chunk as $purchaseOrder) {
                    // perform insertion of the order
                    $sanitized = $this->sanitize((array) $purchaseOrder);

                    // sanity check
                    if ($sanitized !== null) {
                        // Update Or Create those record the fetched from SAGE to keep the data in sync
                        PurchaseOrder::updateOrCreate(
                            [
                                'code' => $sanitized['code']],
                            [
                                'order_date' => $sanitized['order_date'],
                                'estimated_delivery' => $sanitized['estimated_delivery'],
                                'quantity' => $sanitized['quantity'],
                                'date_time_updated' => $sanitized['date_time_updated']

                            ]
                        );
                        $newPurchaseOrders[] = $sanitized;
                    }
                }

                // do the actual insertion of data
                PurchaseOrder::insert($newPurchaseOrders);

                // increment the execution counter
                $chunkCounter++;

                // execution throttling
                // make the server rest after 10 bulk insert
                if ($chunkCounter % 10 === 0) {
                    usleep(100000);
                }
            }
        } catch (Exception $error) {
            $this->sendFailedNotification('Populate Purchase Order From Sage', $error);
        }
    }

    /**
     * Get Purchase Orders from Sage
     *
     * @return Collection
     */
    public function getDataFromBlind(): Collection
    {
        $orders = new PurchaseOrderService();

        $data = $orders->fetchUpdatedData();

        return collect($data);
    }

    /**
     * Sanitize order item coming from SAGE
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $purchaseOrders
     *
     * @return array|null
     */
    public function sanitize(array $sagePurchaseOrder): ?array
    {
        // do a sanity check of the required data
        if (empty($sagePurchaseOrder['Code']) || empty($sagePurchaseOrder['POOrderDate'])
            || empty($sagePurchaseOrder['POEstimatedDelivery']) || empty($sagePurchaseOrder['POQty'])
            || empty($sagePurchaseOrder['DateTimeUpdated'])
        ) {
            return null;
        }

        $purchaseOrder['code'] = $sagePurchaseOrder['Code'];
        $purchaseOrder['order_date'] = $sagePurchaseOrder['POOrderDate'];
        $purchaseOrder['estimated_delivery'] = $sagePurchaseOrder['POEstimatedDelivery'];
        $purchaseOrder['quantity'] = $sagePurchaseOrder['POQty'];
        $purchaseOrder['date_time_updated'] = $sagePurchaseOrder['DateTimeUpdated'];
        $purchaseOrder['created_at'] = now('UTC')->format('Y-m-d H:i:s');

        return $purchaseOrder;
    }
}
