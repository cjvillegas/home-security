<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockItemImageRequest;
use App\Http\Requests\StockItemRequest;
use App\Models\InHouse\StockItems;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Gate;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StockItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $user->permissions = $user->getPermissionNameByModule('process_categories');
        return view('admin.inhouse.stocks-item', compact('user'));
    }

    /**
     * Fetching Stocks
     *
     * @return response
     */
    public function fetchStocks(Request $request)
    {
        $searchString = $request->searchString;
        $stockItems = StockItems::orderBy('id', 'DESC')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('stock_code', 'like', "%{$searchString}%")
                    ->orWhere('range', 'like', "%{$searchString}%")
                    ->orWhere('size', 'like', "%{$searchString}%")
                    ->orWhere('colour', 'like', "%{$searchString}%")
                    ->orWhere('length', 'like', "%{$searchString}%")
                    ->orWhere('status', 'like', "%{$searchString}%");
            });
        $stockItems = $stockItems->paginate($request->size);
        return response()->json(['stockItems' => $stockItems]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockItemRequest $request)
    {
        $stockItem = StockItems::create($request->all());
        $folder = "/uploads/images/stocks";

        if($request->hasFile('product_picture')) {
            $productLink = $request->file('product_picture')
                ->store($folder, 'public');
            Storage::disk('public_uploads')->delete($stockItem->product_picture);
            $stockItem->product_picture = '/storage/' . $productLink;
            $stockItem->save();
        }
        if($request->hasFile('main_location_picture')) {
            $mainLink = $request->file('main_location_picture')
                ->store($folder, 'public');
            Storage::disk('public_uploads')->delete($stockItem->main_location_picture);
            $stockItem->main_location_picture = '/storage/' . $mainLink;
            $stockItem->save();
        }
        if($request->hasFile('secondary_location_picture')) {
            $secondaryLink = $request->file('secondary_location_picture')
                ->store($folder, 'public');
            Storage::disk('public_uploads')->delete($stockItem->secondary_location_picture);
            $stockItem->secondary_location_picture = '/storage/' . $secondaryLink;
            $stockItem->save();
        }
        if($request->hasFile('other_location_picture')) {
            $otherLink = $request->file('other_location_picture')
                ->store($folder, 'public');
            Storage::disk('public_uploads')->delete($stockItem->other_location_picture);
            $stockItem->other_location_picture = '/storage/' . $otherLink;
            $stockItem->save();
        }


        return response()->json(['message' => 'Stock Item successfully saved!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(StockItems $stockItem)
    {
        return response()->json(['stockItem' => $stockItem]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StockItemRequest $request, StockItems $stockItem)
    {
        DB::beginTransaction();
        $folder = "/uploads/images/stocks";
        try {
            $stockItem->update($request->except(['product_picture', 'main_location_picture', 'secondary_location_picture', 'other_location_picture']));
            Log::info($request->hasFile('product_picture'));
            if($request->hasFile('product_picture')) {
                $productLink = $request->file('product_picture')
                    ->store($folder, 'public');

                \Storage::disk('public_uploads')->delete($stockItem->product_picture);
                $stockItem->product_picture = '/storage/' . $productLink;
                $stockItem->save();
            }else{
                //Check if value has changed -> delete file
                if($request->product_picture != $stockItem->product_picture) {
                    \Storage::disk('public_uploads')->delete($stockItem->product_picture);
                    $stockItem->product_picture = null;
                    $stockItem->save();
                }
            }

            if($request->hasFile('main_location_picture')) {
                $mainLink = $request->file('main_location_picture')
                    ->store($folder, 'public');

                \Storage::disk('public_uploads')->delete($stockItem->main_location_picture);
                $stockItem->main_location_picture = '/storage/' . $mainLink;
                $stockItem->save();
            }else{
                //Check if value has changed -> delete file
                if($request->main_location_picture != $stockItem->main_location_picture) {
                    \Storage::disk('public_uploads')->delete($stockItem->main_location_picture);
                    $stockItem->main_location_picture = null;
                    $stockItem->save();
                }
            }

            if($request->hasFile('secondary_location_picture')) {
                $secondaryLink = $request->file('secondary_location_picture')
                    ->store($folder, 'public');
                \Storage::disk('public_uploads')->delete($stockItem->secondary_location_picture);
                $stockItem->secondary_location_picture = '/storage/' . $secondaryLink;
                $stockItem->save();
            }else{
                //Check if value has changed -> delete file
                if($request->secondary_location_picture != $stockItem->secondary_location_picture) {
                    \Storage::disk('public_uploads')->delete($stockItem->secondary_location_picture);
                    $stockItem->secondary_location_picture = null;
                    $stockItem->save();
                }
            }

            if($request->hasFile('other_location_picture')) {
                $otherLink = $request->file('other_location_picture')
                    ->store($folder, 'public');
                \Storage::disk('public_uploads')->delete($stockItem->other_location_picture);
                $stockItem->other_location_picture = '/storage/' . $otherLink;
                $stockItem->save();
            }else{
                //Check if value has changed -> delete file
                if($request->other_location_picture != $stockItem->other_location_picture) {
                    \Storage::disk('public_uploads')->delete($stockItem->other_location_picture);
                    $stockItem->other_location_picture = null;
                    $stockItem->save();
                }
            }
            DB::commit();
            return response()->json(['message' => 'Stock Item successfully updated!']);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            Log::info($e);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockItems $stockItem)
    {
        abort_if(Gate::denies('stock_items_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        \Storage::disk('public_uploads')->delete($stockItem->product_picture);
        \Storage::disk('public_uploads')->delete($stockItem->main_location_picture);
        \Storage::disk('public_uploads')->delete($stockItem->secondary_location_picture);
        \Storage::disk('public_uploads')->delete($stockItem->other_location_picture);
        $stockItem->delete();
        return response()->json(['message' => 'Successfully Deleted!']);
    }
}
