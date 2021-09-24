<?php

namespace App\Console\Commands\Cron;

use App\Abstracts\CronDatabasePopulator;
use App\Models\Order\OrderInvoice;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvoicedOrder extends CronDatabasePopulator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:invoiced-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the invoice data of orders from BlindData database.';

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
            $orderInvoices = $this->getDataFromBlind();

            $chunkCounter = 0;

            // chunk the results to save memory
            foreach ($orderInvoices->chunk(100) as $chunk) {
                // foreach to each instance of retrieved timeclock
                $newOrderInvoices = [];
                foreach ($chunk as $invoice) {
                    // perform data sanitization
                    $sanitized = $this->sanitize((array)$invoice);

                    // sanity check
                    if ($sanitized !== null) {
                        $newOrderInvoices[] = $sanitized;
                    }
                }

                // do the actual insertion of data
                OrderInvoice::insert($newOrderInvoices);

                // increment the execution counter
                $chunkCounter++;

                // execution throttling
                // make the server rest after 10 bulk insert
                if ($chunkCounter % 10 === 0) {
                    usleep(100000);
                }
            }
        } catch (Exception $error) {
            $this->sendFailedNotification('Employee Time Clock', $error);
        }
    }

    /**
     * Retrieve the order invoices data from the BlindData database
     *
     * @return Collection
     */
    protected function getDataFromBlind(): Collection
    {
        $query = "
            SELECT
                DISTINCT([Order].order_id) AS OrderNumber,
                [Order].num_invoice AS InvoiceNumber,
                CAST([Order].dat_invoice AS date) as InvoiceDate
            FROM [User]
	            INNER JOIN [Order] ON [User].id = [Order].user_id
	            INNER JOIN OrderDetail ON [Order].id = OrderDetail.order_id
            WHERE ([Order].dat_invoice BETWEEN CONVERT(DATETIME, '2021-09-01 00:00:00', 102) AND CONVERT(DATETIME, '2021-09-07 00:00:00', 102))
        ";

        // execute the query
        $invoices = DB::connection('sqlsrv')->select($query);

        // return data as collection
        return collect($invoices);
    }

    /**
     * Sanitize order invoice data coming from BlindData.
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $invoice
     *
     * @return array|null
     */
    protected function sanitize(array $invoice): ?array
    {
        // do a sanity check of the required data
        if (empty($invoice['OrderNumber']) || empty($invoice['InvoiceNumber']) || empty($invoice['InvoiceDate'])) {
            return null;
        }

        // build the data to be saved
        $sanitized['order_no'] = $invoice['OrderNumber'];
        $sanitized['invoice_no'] = $invoice['InvoiceNumber'];
        $sanitized['date'] = Carbon::parse($invoice['InvoiceDate'])->format('Y-m-d');

        return $sanitized;
    }
}
