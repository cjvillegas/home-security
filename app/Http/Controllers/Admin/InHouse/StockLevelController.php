<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Http\Controllers\Controller;
use App\Models\StockLevel;
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
     * @return void
     */
    public function fetchStockLevels(Request $request)
    {
        $stockLevels = StockLevel::paginate($request->size);

        return response()->json(['stockLevels' => $stockLevels]);
    }
}
