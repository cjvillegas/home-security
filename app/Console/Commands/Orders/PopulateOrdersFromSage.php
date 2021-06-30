<?php

namespace App\Console\Commands\Orders;

use App\Models\Order;
use Illuminate\Console\Command;

class PopulateOrdersFromSage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:populate-orders-from-sage';

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
        \Log::info('No CRON for the fucking day');

        // clear first the orders table
        $this->clearTable();

        // do insert
        $this->addNewItem([]);

        return 0;
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
     * Inserts new item in the orders table.
     *
     * @param array $sageOrder
     *
     * @return void
     */
    private function addNewItem(array $sageOrder): void
    {
        $order = new Order;
        $order->blind_id = $sageOrder['blind_id'] ?? rand(2, 50000000);
        $order->order_no = $sageOrder['order_no'] ?? rand(2, 50000000);
        $order->customer = $sageOrder['customer'] ?? 'Chaprel John Villegas';
        $order->customer_order_no = $sageOrder['customer_order_no'] ?? \Str::random(40);
        $order->quantity = $sageOrder['quantity'] ?? 10;
        $order->blind_type = $sageOrder['blind_type'] ?? 'Fuck';
        $order->blind_status = $sageOrder['blind_status'] ?? 'FUck you';
        $order->order_entered_by = $sageOrder['order_entered_by'] ?? 1;
        $order->serial_id = $sageOrder['serial_id'] ?? rand(2, 5000000);
        $order->ordered_at = $sageOrder['ordered_at'] ?? now();

        $order->save();
    }
}
