<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\{Employee, Process, Shift, Scanner, Team};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkAnalyticsReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|
     */
    public function index()
    {
        $teams = Team::get();
        $processes = Process::get();
        $shifts = Shift::get();
        $employees = Employee::get();

        return view('admin/reports/work-analytics/index', compact(
            'teams', 'processes', 'shifts', 'employees'
        ));
    }

    /**
     * Retrieve scanners data to be displayed in a work analytics
     *
     * @return JsonResponse
     */
    public function getHourlyAnalytics(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $scanners = Scanner::whereBetween('scannedtime', [$start, $end])
            ->select('scanners.id', 'scanners.scannedtime', 'scanners.employeeid', 'scanners.processid', 'scanners.blindid')
            ->join('processes AS p', 'p.barcode', '=', 'scanners.processid')
            ->with(['employee' => function ($query) {
                $query->select('id', 'fullname', 'barcode', 'team_id', 'shift_id');
            }, 'process' => function ($query) {
                $query->select('id', 'name', 'barcode');
            }])
            ->groupBy('scanners.id')
            ->get();

        return response()->json($scanners);
    }

    /**
     * Retrieve scanners data to be displayed in a work analytics
     *
     * @return JsonResponse
     */
    public function getDailyAnalytics(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $scanners = Scanner::whereBetween('scannedtime', [$start, $end])
            ->select('scanners.id', 'scanners.scannedtime', 'scanners.employeeid', 'scanners.processid', 'scanners.blindid')
            ->join('processes AS p', 'p.barcode', '=', 'scanners.processid')
            ->with(['employee' => function ($query) {
                $query->select('id', 'fullname', 'barcode', 'team_id', 'shift_id');
            }, 'process' => function ($query) {
                $query->select('id', 'name', 'barcode');
            }])
            ->get();

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
            ->whereRaw("`processid` IN ('P1002', 'P5688737')")
            ->first();

        return response()->json($counter);
    }
}
