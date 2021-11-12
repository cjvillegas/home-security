<?php

namespace App\Http\Controllers\PublicAccessible;

use App\Http\Controllers\Controller;
use App\Services\PublicAccessible\PublicDashboardDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('public.public-dashboard');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function ordersData(Request $request)
    {
        $service = new PublicDashboardDataService($request->all());
        $data = $service->getData();

        return response()->json($data);
    }
}
