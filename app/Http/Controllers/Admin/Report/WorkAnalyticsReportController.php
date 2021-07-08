<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Process;
use App\Models\Scanner;
use App\Models\Shift;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            ->with(['employee' => function ($query) {
                $query->select('id', 'fullname', 'barcode', 'team_id', 'shift_id')
                    ->with(['team' => function ($query) {
                        $query->select('id', 'name');
                    }, 'shift' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }, 'process' => function ($query) {
                $query->select('id', 'name', 'barcode');
            }])
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
            ->with(['employee' => function ($query) {
                $query->select('id', 'fullname', 'barcode', 'team_id', 'shift_id')
                    ->with(['team' => function ($query) {
                        $query->select('id', 'name');
                    }, 'shift' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }, 'process' => function ($query) {
                $query->select('id', 'name', 'barcode');
            }])
            ->get();

        return response()->json($scanners);
    }
}
