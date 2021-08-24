<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\MachineCounterReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
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

    /**
     * Machine Counter Report for Dashboard
     *
     * @return JsonResponse
     */
    public function getMachineStatistics()
    {
        $report = new MachineCounterReportService();

        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        return response()->json([
            'todayMachines' => $report->listOfMachines($today),
            'yesterdayMachines' => $report->listOfMachines($yesterday),
            'todayMachineCounterData' => $report->machineCounterData($today),
            'yesterdayMachineCounterData' => $report->machineCounterData($yesterday),
            'todayTotalMachineBoxes' => $report->totalMachineBoxes($today),
            'yesterdayTotalMachineBoxes' => $report->totalMachineBoxes($yesterday)
        ]);

    }
}
