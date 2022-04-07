<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MonitoringRequest;
use App\Models\Monitoring;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.minitoring.index');
    }

    /**
     * Return all monitorings
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        return response()->json(Monitoring::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MonitoringRequest $request
     *
     * @return JsonResponse
     */
    public function store(MonitoringRequest $request): JsonResponse
    {
        $monitoring = new Monitoring();
        $monitoring->name = $request->get('name');
        $monitoring->ip_address = $request->get('ip_address');
        $monitoring->created_by = auth()->user()->id;
        $monitoring->save();

        return response()->json($monitoring);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MonitoringRequest $request
     * @param Monitoring $monitoring
     *
     * @return JsonResponse
     */
    public function update(MonitoringRequest $request, Monitoring $monitoring): JsonResponse
    {
        $monitoring->name = $request->has('name') ? $request->get('name') : $monitoring->name;
        $monitoring->ip_address = $request->has('ip_address') ? $request->get('ip_address') : $monitoring->ip_address;
        $monitoring->save();

        return response()->json($monitoring);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Monitoring $monitoring
     *
     * @return JsonResponse
     */
    public function destroy(Monitoring $monitoring): JsonResponse
    {
        $monitoring->delete();

        return response()->json(true);
    }
}
