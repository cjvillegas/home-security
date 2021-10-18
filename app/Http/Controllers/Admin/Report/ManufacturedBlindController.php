<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CsvExporterService;
use App\Services\Reports\ManufacturedBlindDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManufacturedBlindController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reports.manufactured-blinds');
    }

    /**
     * Fetch all Manufactured Blinds based on DateRange selected
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function getBlinds(Request $request): JsonResponse
    {
        $service = new ManufacturedBlindDataService($request->all());
        $data = $service->getData('list');

        return response()->json([
                'blinds' => $data['blinds'],
                'total_blinds' => $data['totalBlinds'],
                'total_manufactured' => $data['totalManufacturedBlinds'],
                'total_invoice' => $data['totalInvoicedBlinds']
            ]);
    }

    /**
     * Export Manufactured Blinds Data
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function exportManufacturedBlinds(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $service = new ManufacturedBlindDataService($request->all());

        $exporter = new CsvExporterService($user);
        $exporter->setName('Manufactured Blinds Report')
            ->setPath('exports')
            ->setHeaders([
                'date' => 'Date',
                'shift' => 'Shift',
                'manufactured_blinds' => 'Manufactured Blinds',
                'invoiced_blinds' => 'Invoiced Blinds',
            ])
            ->export($service);

        return response()->json([
            'success' => true,
            'message' => 'Your data is being exported. Please wait a while and check the Export page for your export.'
        ]);
    }
}
