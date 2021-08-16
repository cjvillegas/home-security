<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockItemRequest;
use App\Models\InHouse\StockItem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = auth()->user();

        $user->permissions = $user->getPermissionNameByModule('stock_items');

        return view('admin.inhouse.stocks-item', compact('user'));
    }

    /**
     * Fetching Stocks
     *
     * @return JsonResponse
     */
    public function fetchStocks(Request $request)
    {
        $searchString = $request->searchString;

        $StockItems = StockItem::orderBy('id', 'DESC')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('stock_code', 'like', "%{$searchString}%")
                    ->orWhere('range', 'like', "%{$searchString}%")
                    ->orWhere('size', 'like', "%{$searchString}%")
                    ->orWhere('colour', 'like', "%{$searchString}%")
                    ->orWhere('length', 'like', "%{$searchString}%")
                    ->orWhere('status', 'like', "%{$searchString}%");
            });

        $StockItems = $StockItems->paginate($request->size);

        return response()->json(['stockItems' => $StockItems]);
    }

    /**
     * Store Image File
     *
     * @param  mixed $request
     * @param  mixed $stockItem
     * @param  mixed $type
     *
     * @return void
     */
    private function saveImage($request, $stockItem, $type)
    {

        $folder = "/uploads/images/stocks";

        $link = $request->file($type)
            ->store($folder, 'public');
        Storage::disk('public')->delete($stockItem->{$type});
        $stockItem->{$type} = $link;
        $stockItem->save();
    }

    /**
     * Delete Image file from storage
     *
     * @param  mixed $stockItem
     * @param  mixed $type
     * @return void
     */
    private function deleteImage($stockItem, $type)
    {
        Storage::disk('public')->delete($stockItem->{$type});
        $stockItem->{$type} = null;
        $stockItem->save();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreStockItemRequest  $request
     *
     * @return JsonResponse
     */
    public function store(StoreStockItemRequest $request)
    {
        Log::info($request->all());
        DB::beginTransaction();
        try {
            $stockItem = StockItem::create($request->all());

            if ($request->hasFile('product_picture')) {
                $this->saveImage($request, $stockItem, 'product_picture');
            }
            if ($request->hasFile('main_location_picture')) {
                $this->saveImage($request, $stockItem, 'main_location_picture');
            }
            if ($request->hasFile('secondary_location_picture')) {
                $this->saveImage($request, $stockItem, 'secondary_location_picture');
            }
            if ($request->hasFile('other_location_picture')) {
                $this->saveImage($request, $stockItem, 'other_location_picture');
            }

            DB::commit();

            return response()->json(['message' => 'Stock Item successfully saved!']);
        }
        catch ( Exception $e ) {
            DB::rollBack();
            Log::info($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param StockItem $stockItem
     *
     * @return JsonResponse
     */
    public function show(StockItem $stockItem)
    {
        return response()->json(['stockItem' => $stockItem]);
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  StockItem $stockItem
     *
     * @return JsonResponse
     */
    public function update(Request $request, StockItem $stockItem)
    {
        Log::info($request->all());
        $validated = $request->validate(
            [
                'stock_code' => ['required', Rule::unique('stock_items')->ignore($stockItem->id)]
            ]
        );

        DB::beginTransaction();
        try {
            $stockItem->update($request->except(['product_picture', 'main_location_picture', 'secondary_location_picture', 'other_location_picture']));

            if ($request->hasFile('product_picture')) {
                $this->saveImage($request, $stockItem, 'product_picture');
            } else {
                //Check if value has changed -> delete file
                if ($request->product_picture != $stockItem->product_picture) {
                    $this->deleteImage($stockItem, 'product_picture');
                }
            }

            if ($request->hasFile('main_location_picture')) {
                $this->saveImage($request, $stockItem, 'main_location_picture');
            } else {
                //Check if value has changed -> delete file
                if ($request->main_location_picture != $stockItem->main_location_picture) {
                    $this->deleteImage($stockItem, 'main_location_picture');
                }
            }

            if ($request->hasFile('secondary_location_picture')) {
                $this->saveImage($request, $stockItem, 'secondary_location_picture');
            } else {
                //Check if value has changed -> delete file
                if ($request->secondary_location_picture != $stockItem->secondary_location_picture) {
                    $this->deleteImage($stockItem, 'secondary_location_picture');
                }
            }

            if ($request->hasFile('other_location_picture')) {
                $this->saveImage($request, $stockItem, 'other_location_picture');
            } else {
                //Check if value has changed -> delete file
                if ($request->other_location_picture != $stockItem->other_location_picture) {
                    $this->deleteImage($stockItem, 'other_location_picture');
                }
            }

            DB::commit();

            return response()->json(['message' => 'Stock Item successfully updated!']);
        }  catch (Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => $e]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  StockItem $stockItem
     *
     * @return JsonResponse
     */
    public function destroy(StockItem $stockItem)
    {
        abort_if(Gate::denies('stock_items_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Storage::disk('public')->delete($stockItem->product_picture);
        Storage::disk('public')->delete($stockItem->main_location_picture);
        Storage::disk('public')->delete($stockItem->secondary_location_picture);
        Storage::disk('public')->delete($stockItem->other_location_picture);
        $stockItem->delete();

        return response()->json(['message' => 'Successfully Deleted!']);
    }
}
