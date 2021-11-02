<?php

namespace App\Exports\StockOrder;

use App\Models\StockOrder\StockOrder;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use stdClass;
use Throwable;

class StockOrderExport implements
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithStrictNullComparison,
    ShouldQueue
{
    use Exportable;

    /**
     * @var StockOrder
     */
    private $stockOrder;

    /**
     * ExportDataAsCollectionFromService constructor.
     *
     * @param StockOrder $stockOrder
     */
    public function __construct(StockOrder $stockOrder)
    {
        $this->stockOrder = $stockOrder;
    }

    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        $items = $this->getFormattedCollection();

        return $items;
    }

    /**
     * Define the export headers
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Customer Account No',
            'Sales Order Date',
            'Requested Delivery Date',
            'Promised Delivery Date',
            'Customer Order Ref',
            'Transaction Type',
            'Stock Code',
            'Warehouse',
            'Quantity',
            'Unit Sell Price',
            'Nominal Code',
            'Nominal Cost Centre',
            'Nominal Department',
            'Tax Code',
            'Delivery Name',
            'Delivery Address 1',
            'Delivery Address 2',
            'Delivery Address 3',
            'Delivery Address 4',
            'Delivery City',
            'Delivery Country',
            'Delivery Postcode',
            'Delivery Country',
            'Delivery Contact',
            'Delivery Tel Number',
            'Delivery Email',
            'Analysis 1',
            'Analysis 2',
            'Analysis 3',
        ];
    }

    private function getFormattedCollection(): Collection
    {
        $timezone = __env_timezone();
        $items = $this->stockOrder->orderItems()->with('stockLevel')->get();

        return $items->map(function ($item, $key) use ($timezone) {
            $formatted = new stdClass;

            $formatted->customer_account_no = 'STEVE';
            $formatted->sales_order_date = Carbon::parse($item->created_at, 'UTC')->setTimezone($timezone)->format('M d, Y h:i A');
            $formatted->requested_delivery_date = '';
            $formatted->promised_delivery_date = '';
            $formatted->customer_order_ref = $item->order_no;
            $formatted->transaction_type = 'S';
            $formatted->stock_code = $item->stockLevel->code;
            $formatted->warehouse = 'IPSWICH';
            $formatted->quantity = $item->order_qty;
            $formatted->unit_sell_price = 0;
            $formatted->nominal_code = '';
            $formatted->nominal_cost_centre = '';
            $formatted->nominal_department = '';
            $formatted->tax_code = '';
            $formatted->delivery_name = '';
            $formatted->delivery_address_1 = '';
            $formatted->delivery_address_2 = '';
            $formatted->delivery_address_3 = '';
            $formatted->delivery_address_4 = '';
            $formatted->delivery_city = '';
            $formatted->delivery_country_1 = '';
            $formatted->delivery_postcode = '';
            $formatted->delivery_country_2 = '';
            $formatted->delivery_contact = '';
            $formatted->delivery_tel_number = '';
            $formatted->delivery_email = '';
            $formatted->analysis_1 = '';
            $formatted->analysis_2 = '';
            $formatted->analysis_3 = '';

            return $formatted;
        });
    }

    /**
     * Handle when this job fails
     *
     * @param Throwable $exception
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Stock Order Export', [
            'exception' => $exception,
        ]);
    }
}
