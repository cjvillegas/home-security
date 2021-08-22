<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    private function queryString($date, $isTotal = false)
    {
        $query =  "SELECT machines.id, machines.name, machines.created_at, mc.shift_id, mc.id as machine_counter_id,
        SUM(CASE WHEN mc.machine_id = machines.id THEN mc.total_boxes ELSE 0 END) as boxes
        FROM machines
        INNER JOIN machine_counters mc ON mc.machine_id = machines.id
        WHERE mc.created_at BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59'
        ";
        /*
        *if isTotal is true, this query is for getting the OVERALL total boxes per Machine
        *if false, this query is to get total boxes per Shift only (Machine)
        */
        if (!$isTotal) {
            $query .= "\t GROUP BY mc.id";
        } else {
            $query .= "\t GROUP BY machine_id";
        }

        return $query;
    }

    public function getMachineStatistics()
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        // Query today's machine statistics data to get Machine's total boxes
        $todayMachineCounterData = DB::select(
            $this->queryString($today, false)
        );

        $yesterdayMachineCounterData = DB::select(
            $this->queryString($yesterday, false)
        );

        // Get all existing Machine's for Today and Yesterday
        $todayMachines = Machine::whereHas('machineCounters', function($q){
            $q->today();
        })->get();
        $yesterdayMachines = Machine::whereHas('machineCounters', function($q){
            $q->yesterday();
        })->get();

        $todayTotalMachineBoxes = DB::select(
            $this->queryString($today, true)
        );

        $yesterdayTotalMachineBoxes = DB::select(
            $this->queryString($yesterday, true)
        );

        return response()->json([
            'todayMachines' => $todayMachines,
            'yesterdayMachines' => $yesterdayMachines,
            'todayMachineCounterData' => $todayMachineCounterData,
            'yesterdayMachineCounterData' => $yesterdayMachineCounterData,
            'todayTotalMachineBoxes' => $todayTotalMachineBoxes,
            'yesterdayTotalMachineBoxes' => $yesterdayTotalMachineBoxes
        ]);

    }
}
