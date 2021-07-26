<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Database Manager
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MachineController extends Controller
{
    /**
     * Return view for Machine Page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|void
     */
    public function index()
    {
        abort_if(Gate::denies('machine_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.machines.index');
    }

    /**
     * Fetch All Machines Data
     *
     * @return JsonResponse
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
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            Machine::create($request->all());
            DB::commit();

            return response()->json(['message' => 'Successfully Saved']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
        }
    }

    /**
     * Update
     *
     * @param  mixed $request
     * @param  Machine $machine
     *
     * @return JsonResponse
     */
    public function update(Request $request, Machine $machine)
    {
        DB::beginTransaction();
        try {
            $machine->update($request->all());
            DB::commit();

            return response()->json(['message' => 'Successfully Updated!']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
        }
    }

    /**
     * Destroy machine data
     *
     * @param  Machine $machine
     *
     * @return JsonResponse
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
        catch (Exception $e) {
            DB::rollBack();
        }
    }
}
