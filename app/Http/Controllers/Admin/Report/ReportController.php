<?php

namespace App\Http\Controllers\Admin\Report;

use App\Exports\ExportDataAsCollectionFromService;
use App\Http\Controllers\Controller;
use App\Jobs\Exports\ExportQcFaultDataJob;
use App\Models\Export;
use App\Models\User;
use App\Services\CsvExporterService;
use App\Services\MachineCounterReportService;
use App\Services\Reports\QualityControlFaultDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * Navigate to QC Report view
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function qcReport()
    {
        return view('admin.reports.qc-report');
    }

    /**
     * Fetch quality control tagging list
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getQcList(Request $request)
    {
        $service = new QualityControlFaultDataService($request->all());
        $data = $service->getData('list');

        return response()->json($data);
    }

    /**
     * Export Quality Control faults to file
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function exportQcFaultData(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $service = new QualityControlFaultDataService($request->all());
        $service->setColumns([
            'em.fullname AS employee_full_name',
            'us.name AS user_name',
            'qc.qc_code AS quality_control_code',
            'pr.name AS process_name',
            'sc.blindid AS scanner_blind_id',
            'qc_faults.description AS qc_fault_description',
            DB::raw('DATE_FORMAT(qc_faults.created_at, "%b %d, %Y %h:%i %p") AS qc_fault_tag_at'),
            DB::raw('DATE_FORMAT(qc_faults.operation_date, "%b %d, %Y %h:%i %p") AS qc_fault_operation_date'),
        ]);

        $exporter = new CsvExporterService($user);
        $exporter->setName('Quality Control')
            ->setPath('exports')
            ->setHeaders([
                'employee_full_name' => 'Employee',
                'user_name' => 'User',
                'quality_control_code' => 'Quality Control',
                'process_name' => 'Process',
                'scanner_blind_id' => 'Blind ID',
                'qc_fault_description' => 'Description',
                'qc_fault_operation_date' => 'Operation Date',
                'qc_fault_tag_at' => 'Tag At',
            ])
            ->export($service);

        return response()->json([
            'message' => 'Your data is being exported. Please wait a while and check the Export page for your export.',
            'success' => true
        ]);
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
