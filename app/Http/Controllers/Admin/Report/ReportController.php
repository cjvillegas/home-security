<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\MachineCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Navigate to the Data Export page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dataExport()
    {
        abort_if(Gate::denies('data_export_reports_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.reports.data-export-index');
    }

    public function getMachineStatistics()
    {
        $machines_shift_1 = MachineCounter::where('shift_id', '1')->with('machine')->today()->get();
        $machines_shift_2 = MachineCounter::where('shift_id', '2')->with('machine')->today()->get();
        $machines_shift_3 = MachineCounter::where('shift_id', '3')->with('machine')->today()->get();
        $yesterday_shift_1 = MachineCounter::where('shift_id', '1')->with('machine')->yesterday()->get();
        $yesterday_shift_2 = MachineCounter::where('shift_id', '2')->with('machine')->yesterday()->get();
        $yesterday_shift_3 = MachineCounter::where('shift_id', '3')->with('machine')->yesterday()->get();

        $todayMachines = MachineCounter::with('machine')->today()->get();
        Log::info($todayMachines);
        return response()->json([
                'machines_shift_1' => $machines_shift_1,
                'machines_shift_2' => $machines_shift_2,
                'machines_shift_3' => $machines_shift_3,
                'yesterday_shift_1'=> $yesterday_shift_1,
                'yesterday_shift_2'=> $yesterday_shift_2,
                'yesterday_shift_3'=> $yesterday_shift_3,
                'todayMachines' => $todayMachines
                //'yesterdayTotal' => $yesterdayTotal,
        ]);
    }
}
