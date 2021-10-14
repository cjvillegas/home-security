<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Order\OrderInvoice;
use App\Models\Scanner;
use App\Services\Reports\WorkAnalyticsDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class WorkAnalyticsReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('work_analytics_reports_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin/reports/work-analytics/index');
    }

    /**
     * Retrieve scanners data to be displayed in a work analytics
     *
     * @return JsonResponse
     */
    public function getWorkAnalytics(Request $request)
    {
        $service = new WorkAnalyticsDataService($request->all());
        $scanners = $service->getData('all');

        return response()->json($scanners);
    }

    /**
     * Get the daily manufacture reports by shift and the total manufactured blinds
     *
     * @return JsonResponse
     */
    public function manufacturedBlindsAnalytics()
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        $todayAddOne = now()->addDay()->format('Y-m-d');

        // create the query
        $counter = Scanner::select(
                DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 06:00:00' and '{$yesterday} 13:59:59' THEN 1 ELSE 0 END) AS SIGNED) AS yesterday_shift_1"),
                DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 14:00:00' and '{$yesterday} 21:59:59' THEN 1 ELSE 0 END) AS SIGNED) AS yesterday_shift_2"),
                DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 22:00:00' and '{$today} 05:59:59' THEN 1 ELSE 0 END) AS SIGNED) AS yesterday_shift_3"),
                DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 06:00:00' and '{$today} 05:59:59' THEN 1 ELSE 0 END) AS SIGNED) AS yesterday_shift_total"),
                DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 06:00:00' and '{$today} 13:59:59' THEN 1 ELSE 0 END) AS SIGNED) AS today_shift_1"),
                DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 14:00:00' and '{$today} 21:59:59' THEN 1 ELSE 0 END) AS SIGNED) AS today_shift_2"),
                DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 22:00:00' and '{$todayAddOne} 05:59:59' THEN 1 ELSE 0 END) AS SIGNED) AS today_shift_3"),
                DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 06:00:00' and '{$todayAddOne} 05:59:59' THEN 1 ELSE 0 END) AS SIGNED) AS today_shift_total"),
            )
            ->whereIn('processid', ['P1002', 'P5688737', 'P1021', 'P1024', 'P1025'])
            ->first();

        return response()->json($counter);
    }

    /**
     * Get the daily despatch reports of three processes ['P1012', 'P1013', 'P1014']
     * in between three shifts
     *
     * @return JsonResponse
     */
    public function getDespatchDepartmentAnalytics()
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        $todayAddOne = now()->addDay()->format('Y-m-d');

        // create the query
        $counter = Scanner::select(
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 06:00:00' AND '{$yesterday} 13:59:59' AND processid = 'P1012' THEN 1 ELSE 0 END) AS SIGNED) AS shift_1_P1012_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 14:00:00' AND '{$yesterday} 21:59:59' AND processid = 'P1012' THEN 1 ELSE 0 END) AS SIGNED) AS shift_2_P1012_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 22:00:00' AND '{$today} 05:59:59' AND processid = 'P1012' THEN 1 ELSE 0 END) AS SIGNED) AS shift_3_P1012_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 06:00:00' AND '{$today} 05:59:59' AND processid = 'P1012' THEN 1 ELSE 0 END) AS SIGNED) AS total_P1012_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 06:00:00' AND '{$yesterday} 13:59:59' AND processid = 'P1013' THEN 1 ELSE 0 END) AS SIGNED) AS shift_1_P1013_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 14:00:00' AND '{$yesterday} 21:59:59' AND processid = 'P1013' THEN 1 ELSE 0 END) AS SIGNED) AS shift_2_P1013_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 22:00:00' AND '{$today} 05:59:59' AND processid = 'P1013' THEN 1 ELSE 0 END) AS SIGNED) AS shift_3_P1013_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 06:00:00' AND '{$today} 05:59:59' AND processid = 'P1013' THEN 1 ELSE 0 END) AS SIGNED) AS total_P1013_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 06:00:00' AND '{$yesterday} 13:59:59' AND processid = 'P1014' THEN 1 ELSE 0 END) AS SIGNED) AS shift_1_P1014_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 14:00:00' AND '{$yesterday} 21:59:59' AND processid = 'P1014' THEN 1 ELSE 0 END) AS SIGNED) AS shift_2_P1014_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 22:00:00' AND '{$today} 05:59:59' AND processid = 'P1014' THEN 1 ELSE 0 END) AS SIGNED) AS shift_3_P1014_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$yesterday} 06:00:00' AND '{$today} 05:59:59' AND processid = 'P1014' THEN 1 ELSE 0 END) AS SIGNED) AS total_P1014_yesterday"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 06:00:00' AND '{$today} 13:59:59' AND processid = 'P1012' THEN 1 ELSE 0 END) AS SIGNED) AS shift_1_P1012_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 14:00:00' AND '{$today} 21:59:59' AND processid = 'P1012' THEN 1 ELSE 0 END) AS SIGNED) AS shift_2_P1012_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 22:00:00' AND '{$todayAddOne} 05:59:59' AND processid = 'P1012' THEN 1 ELSE 0 END) AS SIGNED) AS shift_3_P1012_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 06:00:00' AND '{$todayAddOne} 05:59:59' AND processid = 'P1012' THEN 1 ELSE 0 END) AS SIGNED) AS total_P1012_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 06:00:00' AND '{$today} 13:59:59' AND processid = 'P1013' THEN 1 ELSE 0 END) AS SIGNED) AS shift_1_P1013_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 14:00:00' AND '{$today} 21:59:59' AND processid = 'P1013' THEN 1 ELSE 0 END) AS SIGNED) AS shift_2_P1013_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 22:00:00' AND '{$todayAddOne} 05:59:59' AND processid = 'P1013' THEN 1 ELSE 0 END) AS SIGNED) AS shift_3_P1013_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 06:00:00' AND '{$todayAddOne} 05:59:59' AND processid = 'P1013' THEN 1 ELSE 0 END) AS SIGNED) AS total_P1013_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 06:00:00' AND '{$today} 13:59:59' AND processid = 'P1014' THEN 1 ELSE 0 END) AS SIGNED) AS shift_1_P1014_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 14:00:00' AND '{$today} 21:59:59' AND processid = 'P1014' THEN 1 ELSE 0 END) AS SIGNED) AS shift_2_P1014_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 22:00:00' AND '{$todayAddOne} 05:59:59' AND processid = 'P1014' THEN 1 ELSE 0 END) AS SIGNED) AS shift_3_P1014_today"),
            DB::raw("CAST(SUM(CASE WHEN `scannedtime` BETWEEN '{$today} 06:00:00' AND '{$todayAddOne} 05:59:59' AND processid = 'P1014' THEN 1 ELSE 0 END) AS SIGNED) AS total_P1014_today"),
        )
            ->whereIn('processid', ['P1012', 'P1013', 'P1014'])
            ->first()
            ->toArray();

        // get yesterdays invoiced orders
        $total_invoiced_orders_yesterday = DB::select("
            SELECT COUNT(*) AS aggregate from(
                SELECT COUNT(DISTINCT order_invoices.id)
                FROM `order_invoices`
                    INNER JOIN `orders` AS `o` ON `o`.`order_no` = `order_invoices`.`order_no`
                    INNER JOIN `scanners` AS `sc` ON `sc`.`blindid` = `o`.`serial_id` AND sc.processid IN ('P1012', 'P1013', 'P1014')
                WHERE `order_invoices`.`date` = '{$yesterday}' AND `order_invoices`.`deleted_at` IS NULL
                    GROUP BY order_invoices.id
            ) AS fu
        ")[0]->aggregate;

        $total_shipped_consignments_yesterday = DB::select("
            SELECT COUNT(*) AS aggregate FROM(
                SELECT COUNT(orders.order_no)
                FROM orders
                    INNER JOIN order_trackings ot ON ot.order_no = orders.order_no
                WHERE DATE_FORMAT(ot.created_at, '%Y-%m-%d') = '2021-09-06'
                    GROUP BY orders.order_no
            ) AS fu
        ")[0]->aggregate;

        $counter = array_merge($counter,
            [
                'total_invoiced_orders_yesterday' => $total_invoiced_orders_yesterday,
                'total_shipped_consignments_yesterday' => $total_shipped_consignments_yesterday
            ]);

        return response()->json($counter);
    }
}
