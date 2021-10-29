<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Scanner;
use App\Models\User;
use App\Services\CsvExporterService;
use App\Services\Reports\FireRegisterDataService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $employees = [];

        $service = new FireRegisterDataService($request->all());
        $employees = $service->getData('list');

        return response()->json(['employees' => $employees]);
    }

    public function exportFireRegister(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $service = new FireRegisterDataService($request->all());

        $exporter = new CsvExporterService($user);
        $exporter->setName('Fire Register')
            ->setPath('exports')
            ->setHeaders([
                'id' => 'Employee ID',
                'fullname' => 'Fullname',
                'scannedtime' => 'Operation Started At',
                'clock_num' => 'Clock Num',
                'clock_in' => 'Clock In',
                'clock_out' => 'Clock Out',
                'time_in'=> 'Time In'
            ])
            ->export($service);

        return response()->json([
            'success' => true,
            'message' => 'Your data is being exported. Please wait a while and check the Export page for your export.'
        ]);
    }
}
