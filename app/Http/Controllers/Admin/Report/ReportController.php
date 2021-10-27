<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CsvExporterService;
use App\Services\MachineCounterReportService;
use App\Services\Reports\DashboardMachineStatisticsDataService;
use App\Services\Reports\QualityControlFaultDataService;
use App\Services\Reports\TargetPerformanceDataService;
use App\Services\Reports\TeamStatusDataService;
use App\Services\Reports\TimeclockDataService;
use App\Services\Reports\WhoWorksHereDataService;
use App\Services\TargetPerformanceExporterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function teamStatus()
    {
        return view('admin.reports.team-status-report');
    }

    /**
     * Fetch team status data report
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getTeamStatusReport(Request $request)
    {
        $service = new TeamStatusDataService($request->all());
        $data = $service->getData('list');

        return response()->json($data);
    }

    /**
     * Export team status data to a CSV file
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function exportTeamStatus(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $service = new TeamStatusDataService($request->all());

        $exporter = new CsvExporterService($user);
        $exporter->setName('Team Status Report')
            ->setPath('exports')
            ->setHeaders([
                'folder_name' => 'Folder Name',
                'planned_blinds' => 'Planned Blinds',
                'not_started' => 'Not Started',
                'started_blinds' => 'Started Blinds',
                'completed_blinds' => 'Completed Blinds',
                'packed_blinds' => 'Packed Blinds',
            ])
            ->export($service);

        return response()->json([
            'success' => true,
            'message' => 'Your data is being exported. Please wait a while and check the Export page for your export.'
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

    /**
     * Time & Attendance Report Page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function timeAndAttendancePage()
    {
        return view('admin.reports.time-and-attendance');
    }

    /**
     * Fetch time and attendance data
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function timeAndAttendance(Request $request)
    {
        $service = new TimeclockDataService($request->all());
        $data = $service->getData('list');

        return response()->json($data);
    }

    /**
     * Export team status data to a CSV file
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function exportTimeAndAttendance(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $service = new TimeclockDataService($request->all());

        $exporter = new CsvExporterService($user);
        $exporter->setName('Time And Attendance Report')
            ->setPath('exports')
            ->setHeaders([
                'employee_name' => 'Employee Name',
                'clock_number' => 'Clock No.',
                'clock_in' => 'Clock In',
                'clock_out' => 'Clock Out',
                'time_in' => 'Time In',
            ])
            ->export($service);

        return response()->json([
            'success' => true,
            'message' => 'Your data is being exported. Please wait a while and check the Export page for your export.'
        ]);
    }

    /**
     * Who Works Here report page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function whoWorksHerePage()
    {
        return view('admin.reports.who-works-here');
    }

    /**
     * Fetch the who works here data
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function whoWorksHere(Request $request): JsonResponse
    {
        $service = new WhoWorksHereDataService($request->all());
        $data = $service->getData('list');

        return response()->json($data);
    }

    /**
     * Retrieve Dashboard's machine statistics data
     *
     * @return JsonResponse
     */
    public function dashboardMachineStatistics(Request $request): JsonResponse
    {
        $service = new DashboardMachineStatisticsDataService($request->all());
        $data = $service->getData('list');

        return response()->json($data);
    }

    /**
     * Target Performance
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function targetPerformance()
    {
        return view('admin.reports.target-performance');
    }

    /**
     * Get Performances based on Selected filters
     *
     * @return JsonResponse
     */
    public function getTargetPerformances(Request $request): JsonResponse
    {
        $service = new TargetPerformanceDataService($request->all());
        $data = $service->getData('list');

        return response()->json(
            [
                'performances' => $data['performances'],
                'dates' => $data['dates']
            ]
        );
    }

    /**
     * Export Target Performance
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function exportTargetPerformance(Request $request): JsonResponse
    {
        $user = User::find(auth()->user()->id);

        $service = new TargetPerformanceDataService($request->all());
        $exporter = new TargetPerformanceExporterService($user);
        $exporter->setName('Target Performance Report')
            ->setPath('exports')
            ->export($service, $request->dateRange);

        return response()->json(
            [
                'success' => true,
                'message' => 'Your data is being exported. Please wait a while and check the Export page for your export.'
            ]
        );
    }
}
