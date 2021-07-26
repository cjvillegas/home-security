<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\{Employee, Process, Shift, Scanner, Team};
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
     * @return \Illuminate\Contracts\View\View|
     */
    public function index()
    {
        abort_if(Gate::denies('work_analytics_reports_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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
    public function getWorkAnalytics(Request $request)
    {
        try {
            $start = $request->get('start');
            $end = $request->get('end');

            $scanners = Scanner::whereBetween('scannedtime', [$start, $end])
                ->select(
                    'scanners.id',
                    'scanners.scannedtime',
                    'scanners.employeeid',
                    'scanners.processid',
                    'scanners.blindid',
                    'e.id AS employee_id',
                    'e.fullname AS fullname',
                    'e.team_id',
                    'e.shift_id',
                    'p.barcode AS process_barcode',
                    'p.id AS process_id'
                )
                ->join('processes AS p', 'p.barcode', '=', 'scanners.processid')
                ->join('employees AS e', 'e.barcode', '=', 'scanners.employeeid')
                ->get();

            return response()->json($scanners);
        } catch (\Exception $e) {
            \Log::error('Get Work Analytics Error', [
                'error ' . $e
            ]);
        }
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

        $today = \Carbon\Carbon::parse('2021-07-15')->format('Y-m-d');
        $yesterday = \Carbon\Carbon::parse('2021-07-14')->subDay()->format('Y-m-d');
        $todayAddOne = \Carbon\Carbon::parse('2021-07-16')->addDay()->format('Y-m-d');

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
            ->first();

        return response()->json($counter);
    }
}
