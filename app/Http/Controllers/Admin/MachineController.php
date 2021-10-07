<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MachineRequest;
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory
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
    public function fetchMachines(Request $request)
    {
        $searchString = $request->searchString;
        $size = $request->size;

        $machines = Machine::orderBy('created_at', 'desc')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('name', 'like', "%{$searchString}%")
                    ->orWhere('serial_no', 'like', "%{$searchString}%")
                    ->orWhere('location', 'like', "%{$searchString}%")
                    ->orWhere('status', 'like', "%{$searchString}%");
            });
        $machines = $machines->paginate($size);

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
    public function store(MachineRequest $request)
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

            return response()->json(['message' => "Something went wrong when creating a new machine."], 500);
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
    public function update(MachineRequest $request, Machine $machine)
    {
        abort_if(Gate::denies('machine_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $machine->update($request->all());
            DB::commit();

            return response()->json(['message' => 'Successfully Updated!']);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::info($e);

            return response()->json(['message' => "Something went wrong when updating machine."], 500);
        }
    }

    /**
     * Destroy a machine
     *
     * @param  Machine $machine
     *
     * @return JsonResponse
     */
    public function destroy(Machine $machine): JsonResponse
    {
        abort_if(Gate::denies('machine_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $machine->delete();
        DB::commit();

        return response()->json(['message' => 'Successfully Deleted!']);
    }

    /**
     * Fetch all machines
     *
     * @return JsonResponse
     */
    public function machines(): JsonResponse
    {
        $machines = Machine::
            select([
                'id',
                'location',
                'model',
                'name',
                'serial_no'
            ])
            ->get();

        return response()->json($machines);
    }
}
