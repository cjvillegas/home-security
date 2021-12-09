<?php

namespace App\Repositories\Orders;

use App\Models\Order;

class OrderRepository
{
    /**
     * Searches orders based on the passed field name
     *
     * @param string $field
     * @param string $searchString
     *
     * @return array
     */
    public function searchOrdersByField(string $field, string $searchString): array
    {
        // sanity checks if the required fields are provided
        if (!$field || !$searchString) {
            return [];
        }

        // do the actual query
        $orders = Order::where($field, 'like', "%$searchString%")
            ->select('id', 'blind_id', 'order_no', 'customer', 'serial_id', 'customer_order_no')
            ->groupBy('order_no')
            ->limit(25)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        return $orders;
    }

    /**
     * Base query that we will run to import data from Blind Data
     *
     * @param string $where
     * @param int|null $limit
     *
     * @return string
     */
    public function generateBaseQueryForBlindData(string $where = null, int $limit = null): string
    {
        $query = "
            SELECT
                sdl.id AS SerialID,
                o.order_id AS OrderNo,
                o.dat_required AS RequiredDate,
                u.company AS Customer,
                o.cust_ref AS CustRef,
                o.cust_no AS CustNo,
                CASE
                    WHEN ml.location = 'Aluminium' THEN 'Aluminium'
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
        ";

        // append the where conditions
        if (!empty($where)) {
            $query .= "\t WHERE {$where}";
        }

        // add the limit
        if (!empty($limit)) {
            $query .= "\t LIMIT {$limit}";
        }

        return $query;
    }
}
