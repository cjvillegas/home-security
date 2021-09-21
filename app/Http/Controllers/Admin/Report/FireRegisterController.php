<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Scanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class FireRegisterController extends Controller
{
    /**
     * Return a landing page for Fire Register
     *
     * @return View
     */
    public function fireRegister(): View
    {
        return view('admin.reports.fire-register');
    }

    /**
     * Fetch all Employee lists based on selected shift
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function getEmployees(Request $request): JsonResponse
    {
        $query = "SELECT e.fullname, sc.scannedtime
            FROM employees e
            INNER JOIN scanners sc ON sc.employeeid = e.barcode
            ";

        //$employees = DB::select($query);
        Log::info($request->all());
        $from = date('Y-m-d').' '.$request->from;

        //if selected shift is 3, it should fetch for tomorrow's date.
        $to = $request->shifts == 3 ? date('Y-m-d', strtotime("+1 day")).' '.$request->to : date('Y-m-d').' '.$request->to;
        Log::info($from); Log::info($to);
        $employees = DB::table('employees')
                        ->select('employees.fullname', 'scanners.scannedtime')
                        ->join('scanners', 'employees.barcode', '=', 'scanners.employeeid')
                        ->whereBetween('scannedtime', [$from, $to])
                        ->groupBy('employees.id', 'scanners.employeeid')
                        ->get();

        return response()->json(['employees' => $employees]);
    }
}
