<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\Reports\ManufacturedBlindDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManufacturedBlindController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.reports.manufactured-blinds');
    }

    /**
     * Fetch all Manufactured Blinds based on Date selected
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function getBlinds(Request $request)
    {
        $service = new ManufacturedBlindDataService($request->all());
        $blinds = $service->getData('list');

        return response()->json(['blinds' => $blinds]);
    }

    /**
     * Export Manufactured Blinds Data
     *
     * @param  mixed $request
     *
     * @return null
     */
    public function exportManufacturedBlinds(Request $request)
    {
        return null;
    }
}
