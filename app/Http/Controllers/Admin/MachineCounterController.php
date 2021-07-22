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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('machine_counter_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.machine-counters.index');
    }

    public function fetch()
    {
        $machineCounters = MachineCounter::with('machine')
            ->with('employee')->with('team')
            ->with('shift')->orderBy('id', 'DESC')->get();
        $machines = Machine::all();
        $employees = Employee::all();
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            return response()->json(
                [
                    'message' => 'Successfully Saved!'
                ], 200
            );
        }
        catch ( Exception $e ) {
            DB::rollBack();
            Log::info($e);
        }

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        DB::beginTransaction();
        try {
            MachineCounter::find($id)->update($request->all());
            DB::commit();
            return response()->json(['message' => 'Successfully Updated'], 200);
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
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            MachineCounter::find($id)->delete();
            DB::commit();

            return response()->json(['message' => 'Successfully Deleted!'], 200);
        }
        catch ( Exception $e ) {
            DB::rollBack();
        }


    }
}
