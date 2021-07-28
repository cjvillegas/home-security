<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MachineCounterRequest;
use App\Models\Employee;
use App\Models\Machine;
use App\Models\MachineCounter;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class MachineCounterController extends Controller
{
    /**
     * Return view for Machine Counter page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('machine_counter_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.machine-counters.index');
    }

    /**
     * Fetch All Machine Counters
     *
     * @return JsonResponse
     */
    public function fetchMachineCounters()
    {
        $machineCounters = MachineCounter::with('machine')
            ->with('employee')->with('team')
            ->with('shift')->orderBy('id', 'DESC')->paginate(request()->size);
        $machines = Machine::all();
        $employees = Employee::orderBy('id', 'DESC')->get()->take(10);
        $teams = Team::all();

        return response()->json(
            [
                'employees' => $employees,
                'teams' => $teams,
                'machines' => $machines,
                'machineCounters' => $machineCounters
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  MachineCounterRequest  $request
     *
     * @return JsonResponse
     */
    public function store(MachineCounterRequest $request)
    {
        DB::beginTransaction();
        try {
            MachineCounter::create($request->all());
            DB::commit();

            return response()->json(['message' => 'Successfully Saved!']);
        }
        catch (Exception $e) {
            DB::rollBack();

            Log::info($e);

            return response()->json(['message' => "Something went wrong when creating a new machine counter."], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  MachineCounter  $machineCounter
     *
     * @return JsonResponse
     */
    public function update(MachineCounterRequest $request, MachineCounter $machineCounter)
    {
        DB::beginTransaction();
        try {
            $machineCounter->update($request->all());
            DB::commit();

            return response()->json(['message' => 'Successfully Updated']);
        }
        catch (Exception $e) {
            DB::rollBack();

            Log::info($e);

            return response()->json(['message' => "Something went wrong when updating machine counter."], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  MachineCounter $machineCounter
     *
     * @return JsonResponse
     */
    public function destroy(MachineCounter $machineCounter)
    {
        DB::beginTransaction();
        try {
            $machineCounter->delete();
            DB::commit();

            return response()->json(['message' => 'Successfully Deleted!']);
        }
        catch (Exception $e) {
            DB::rollBack();
        }
    }
}
