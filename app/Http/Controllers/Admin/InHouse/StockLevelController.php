<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Http\Controllers\Controller;
use App\Models\StockLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = auth()->user();

        $user->permissions = $user->getPermissionNameByModule('process_categories');

        return view('admin.inhouse.stock-level', compact('user'));
    }

    /**
     * Fetch Stock Level
     *
     * @param  mixed $request
     *
     * @return JsonResponse
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
