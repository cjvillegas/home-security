<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMssqlConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mssql-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test MSSQL Connection';

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
        // Test database connection
        try {
            \DB::connection('sqlsrv')->getPdo();

            $test = \DB::connection('sqlsrv')->select("
                SELECT TOP 5
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
                    OrderDetail INNER JOIN
                    [Order] ON OrderDetail.order_id = [Order].id INNER JOIN
                    [User] ON [Order].user_id = [User].id INNER JOIN
                    BlindType ON OrderDetail.blindtype_id = BlindType.id INNER JOIN
                    Fabric ON OrderDetail.fabric_id = Fabric.id INNER JOIN
                    OrderStatus ON [Order].orderstatus_id = OrderStatus.id INNER JOIN
                    DetailStatus ON OrderDetail.detailstatus_id = DetailStatus.id INNER JOIN
                    ManLocation ON BlindType.manlocation_id = ManLocation.id INNER JOIN
                    SerialDetailLine ON OrderDetail.id = SerialDetailLine.OrderDetail_id LEFT OUTER JOIN
                    RollerTable ON OrderDetail.RollerTableID = RollerTable.ID
                WHERE
                    ([Order].order_id IS NOT NULL)
                    AND (OrderStatus.IsOrder = '1')
                    AND (OrderStatus.IsQuotation = '0')
                    AND (OrderStatus.id NOT BETWEEN '5' AND '7')
                    AND (BlindType.id <> '382')
            ");

            $test = collect($test);
            dd($test);
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e );
        }

        return 0;
    }
}
