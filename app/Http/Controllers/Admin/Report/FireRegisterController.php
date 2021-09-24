<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Scanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
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
        abort_if(Gate::denies('fire_register_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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
        abort_if(Gate::denies('fire_register_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $from = date('Y-m-d').' '.$request->from;
        //if selected shift is 3, it should fetch for tomorrow's date.
        $to = $request->shifts == 3 ? date('Y-m-d', strtotime("+1 day")).' '.$request->to : date('Y-m-d').' '.$request->to;
        $employees = DB::table('employees')
                        ->select('employees.fullname', 'scanners.scannedtime', 'employees.clock_num')
                        ->join('scanners', 'employees.barcode', '=', 'scanners.employeeid')
                        ->whereBetween('scannedtime', [$from, $to])
                        ->groupBy('employees.id', 'scanners.employeeid')
                        ->get();

        return response()->json(['employees' => $employees]);
    }
}
