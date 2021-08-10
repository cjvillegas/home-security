<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Http\Controllers\Controller;
use App\Models\StockLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;

class StockLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('stock_level_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();

        $user->permissions = $user->getPermissionNameByModule('stock_level');

        return view('admin.inhouse.stock-level', compact('user'));
    }

    /**
     * Fetch Stock Level
     *
     * @param  mixed $request
     * @return void
     */
    public function fetchStockLevels(Request $request)
    {
        $searchString = $request->searchString;
        $size = $request->size;

        $stockLevels = StockLevel::orderBy('created_at', 'DESC')
            ->when($searchString, function($query) use ($searchString) {
                $query->where('name', 'like', "%{$searchString}%")
                    ->orWhere('code', 'like', "%{$searchString}%");
            });

        $stockLevels = $stockLevels->paginate($size);

        return response()->json(['stockLevels' => $stockLevels]);
    }

    /**
     * Fetch Last Sync
     *
     * @return JsonResponse
     */
    public function lastSync()
    {
        $lastSync = StockLevel::select('created_at')->latest('created_at')->first();

        return response()->json(['lastSync' => $lastSync]);
    }
}
