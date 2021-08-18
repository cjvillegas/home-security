<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\QualityControlRequest;
use App\Models\QualityControl;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class QualityControlController extends Controller
{
    /**
     * This will be the quality control's index page. After a successful authentication
     * and no referrer route, the employee will be redirect directly here.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('quality_control_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();

        $user->permissions = $user->getPermissionNameByModule('stock_items');

        return view('admin.quality-control.index', compact('user'));
    }

    /**
     * Fetch Quality Controls' List
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchQualityControls(Request $request)
    {
        $searchString = $request->searchString;

        $qualityControls = QualityControl::orderBy('id', 'DESC')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('qc_code', 'like', "%{$searchString}%");
            });

        $qualityControls = $qualityControls->paginate($request->size);

        return response()->json(['qualityControls' => $qualityControls]);
    }

    /**
     * Store Quality Control Information
     * @param QualityControlRequest $request
     * @return JsonResponse
     */
    public function store(QualityControlRequest $request)
    {
        abort_if(Gate::denies('quality_control_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            QualityControl::create($request->all());
            DB::commit();

            return response()->json(['message' => 'Quality Control Successfully Saved']);
        }
        catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Update Quality Control Information
     * @param QualityControlRequest $request
     * @param QualityControl $qualityControl
     * @return JsonResponse
     */
    public function update(QualityControlRequest $request, QualityControl $qualityControl)
    {
        abort_if(Gate::denies('quality_control_update'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $qualityControl->update($request->all());
            DB::commit();
            return response()->json(['message' => 'Quality Control Successfully Updated']);
        }
        catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Delete Quality Control
     *
     * @param  QualityControl $qualityControl
     * @return JsonResponse
     */
    public function destroy(QualityControl $qualityControl)
    {
        abort_if(Gate::denies('quality_control_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $qualityControl->delete();
    }
}
