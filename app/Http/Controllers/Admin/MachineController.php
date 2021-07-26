<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MachineRequest;
use App\Models\Machine;
use Exception;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Database Manager
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MachineController extends Controller
{
    /**
     * Return view for Machine Page
     *
     * @return void
     */
    public function index()
    {
        abort_if(Gate::denies('machine_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.machines.index');
    }

    /**
     * Fetch All Machines Data
     *
     * @return void
     */
    public function fetchMachines()
    {
        $machines = Machine::paginate(request()->size);

        return response()->json(['machines' => $machines]);
    }

    /**
     * Store
     * Save Machine info
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function store(MachineRequest $request)
    {
        DB::beginTransaction();
        try {
            Machine::create($request->all());
            DB::commit();
            return response()->json(['message' => 'Successfully Saved']);
        }
        catch ( Exception $e ) {
            DB::rollBack();
            Log::info($e);
        }
    }

    /**
     * Update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(MachineRequest $request, Machine $machine)
    {
        abort_if(Gate::denies('machine_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $machine->update($request->all());
            DB::commit();
            return response()->json(['message' => 'Successfully Updated!']);
        }
        catch ( Exception $e ) {
            DB::rollBack();
            Log::info($e);
        }
    }

    /**
     * Destroy machine data
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy(Machine $machine)
    {
        abort_if(Gate::denies('machine_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $machine->delete();
            DB::commit();
            return response()->json(['message' => 'Successfully Deleted!']);
        }
        catch ( Exception $e ) {
            DB::rollBack();
        }
    }
}
