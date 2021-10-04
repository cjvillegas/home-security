<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProcessSequence\ProcessSequence;
use App\Services\Reports\ManufacturedBlindDataService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use stdClass;

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
     * Fetch all Manufactured Blinds based on DateRange selected
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function getBlinds(Request $request): JsonResponse
    {
        $service = new ManufacturedBlindDataService();
        $blinds = $service->getAllBlinds($request->dateRange);

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
