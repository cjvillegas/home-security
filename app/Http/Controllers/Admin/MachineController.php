<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MachineController extends Controller
{
    public function index()
    {
        return view('admin.machines.index');
    }

    /**
     * Fetch All Machines Data
     *
     * @return void
     */
    public function fetchMachines()
    {
        $machines = Machine::all();
        return response()->json(['machines' => $machines], 200);
    }

    /**
     * Store
     * Save Machine info
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            Machine::create($request->all());
            DB::commit();
            return response()->json(['message' => 'Successfully Saved'], 200);
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
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            Machine::find($id)->update($request->all());
            DB::commit();
            return response()->json(['message' => 'Successfully Updated!'], 200);
        }
        catch ( Exception $e ) {
            DB::rollBack();
            Log::info($e);
        }
    }
}
