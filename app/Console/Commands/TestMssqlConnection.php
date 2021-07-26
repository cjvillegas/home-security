<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
     * @return void
     */
    public function handle(): void
    {
        // Test database connection
        try {
            DB::connection('sqlsrv')->getPdo();

            $test = DB::connection('sqlsrv')->select($this->getQueryWithSerialId());

            $test = collect($test);

            $this->info('Connection successful');
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e );
        }
    }

    public function getQueryWithSerialId($loadAll = false): string
    {
        $latestSerialId = Order::getLatestSerialId();

        // initialize the query
        $query = "
            SELECT
                TOP 10
                OrderDetail.id AS BlindId,
                [Order].order_id AS OrderNo,
                [User].company AS Customer,
                [Order].cust_no AS CustOrdNo,
                OrderDetail.quantity AS Quantity,
                BlindType.description AS BlindCode,
                DetailStatus.name AS BlindStatus,
                [Order].dat_delivery AS DespatchDate,
                [Order].dat_order AS Ordered,
                [Order].username AS OrderEnteredBy,
                SerialDetailLine.id AS SerialID,
                [Category].name AS CategoryName
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
            $query .= "\t AND (OrderDetail.id > {$latestSerialId})";
        }

        // return data as collection
        return $query;
    }

    public function testQuery()
    {
        return "
            SELECT
                TOP 10
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
                SerialDetailLine.id AS SerialID,
                [Category].name AS CategoryName
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
    }
}
