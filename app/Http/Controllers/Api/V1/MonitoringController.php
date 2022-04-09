<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Monitoring;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('test');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Update block status
     *
     * @param Request $request
     * @param Monitoring $monitoring
     *
     * @return JsonResponse
     */
    public function statusChange(Request $request, Monitoring $monitoring)
    {
        $monitoring->status = $request->has('status') ? $request->get('status') : $monitoring->status;
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
