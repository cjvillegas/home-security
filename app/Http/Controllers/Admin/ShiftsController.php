<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyShiftRequest;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use App\Models\Shift;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ShiftsController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('team_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();

        $user->permissions = $user->getPermissionNameByModule('stock_items');

        return view('admin.shifts.index', compact('user'));
    }

    /**
     * Fetch Shifts. API
     *
     * @return void
     */
    public function fetchShifts(Request $request)
    {
        $searchString = $request->searchString;
        $size = $request->size;

        $shifts = Shift::orderBy('name', 'asc')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('name', 'like', "%{$searchString}%");
            });
        $shifts = $shifts->paginate($size);

        return response()->json(['shifts' => $shifts]);
    }

    /**
     * Save/Store Shift
     *
     * @param  StoreShiftRequest $request
     * @return JsonResponse
     */
    public function store(StoreShiftRequest $request)
    {
        $shift = Shift::create($request->all());

        return response()->json(['message' => 'Shift Successfully Saved.']);
    }

    /**
     * Update Shift's Information
     *
     * @param  UpdateShiftRequest $request
     * @param  Shift $shift
     * @return JsonResponse
     */
    public function update(UpdateShiftRequest $request, Shift $shift)
    {
        $shift->update($request->all());

        return response()->json(['message' => 'Shift Successfully Updated.']);
    }

    /**
     * Delete/Destroy Shift
     *
     * @param  Shift $shift
     * @return JsonResponse
     */
    public function destroy(Shift $shift)
    {
        abort_if(Gate::denies('shift_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shift->delete();

        return response()->json(['message' => 'Shift Successfully Deleted.']);
    }

    public function massDestroy(MassDestroyShiftRequest $request)
    {
        Shift::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
