<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MachineCounterRequest;
use App\Models\Employee;
use App\Models\Machine;
use App\Models\MachineCounter;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class MachineCounterController extends Controller
{
    /**
     * Return view for Machine Counter page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('machine_counter_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.machine-counters.index');
    }

    /**
     * Fetch All Machine Counters
     *
     * @return void
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
            ], 200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MachineCounterRequest $request)
    {
        DB::beginTransaction();
        try {
            MachineCounter::create($request->all());
            DB::commit();
            return response()->json(['message' => 'Successfully Saved!']);
        }
        catch ( Exception $e ) {
            DB::rollBack();
            Log::info($e);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MachineCounter $machineCounter)
    {
        DB::beginTransaction();
        try {
            $machineCounter->update($request->all());
            DB::commit();
            return response()->json(['message' => 'Successfully Updated']);
        }
        catch ( Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MachineCounter $machineCounter)
    {
        DB::beginTransaction();
        try {
            $machineCounter->delete();
            DB::commit();
            return response()->json(['message' => 'Successfully Deleted!']);
        }
        catch ( Exception $e ) {
            DB::rollBack();
        }


    }
}
