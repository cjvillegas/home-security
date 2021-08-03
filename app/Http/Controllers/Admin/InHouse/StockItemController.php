<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockItemImageRequest;
use App\Http\Requests\StockItemRequest;
use App\Models\InHouse\StockItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Gate;
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
        $stockItems = StockItems::paginate($request->size);
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
        Log::info($request->all());
        $stockItem = StockItems::create($request->all());
        $folder = "/uploads/images/stocks";

        if($request->hasFile('product_picture')) {
            $productLink = $request->file('product_picture')
                ->store($folder, 'public_uploads');
            Storage::disk('public')->delete($stockItem->product_picture);
            $stockItem->product_picture = '/' . $productLink;
            $stockItem->save();
        }
        if($request->hasFile('main_location_picture')) {
            $mainLink = $request->file('main_location_picture')
                ->store($folder, 'public_uploads');
            Storage::disk('public')->delete($stockItem->main_location_picture);
            $stockItem->main_location_picture = '/' . $mainLink;
            $stockItem->save();
        }
        if($request->hasFile('secondary_location_picture')) {
            $secondaryLink = $request->file('secondary_location_picture')
                ->store($folder, 'public_uploads');
            Storage::disk('public')->delete($stockItem->secondary_location_picture);
            $stockItem->secondary_location_picture = '/' . $secondaryLink;
            $stockItem->save();
        }
        if($request->hasFile('other_location_picture')) {
            $otherLink = $request->file('other_location_picture')
                ->store($folder, 'public_uploads');
            Storage::disk('public')->delete($stockItem->other_location_picture);
            $stockItem->other_location_picture = '/' . $otherLink;
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
        $folder = "/uploads/images/stocks";


        $stockItem->update($request->except(['product_picture', 'main_location_picture', 'secondary_location_picture', 'other_location_picture']));

        if($request->hasFile('product_picture')) {
            $productLink = $request->file('product_picture')
                ->store($folder, 'public_uploads');

            \Storage::disk('public_uploads')->delete( $stockItem->product_location_picture);
            $stockItem->product_picture = '/' . $productLink;
            $stockItem->save();
        }else{
            //Check if value has changed -> delete file
            if($request->product_location_picture != $stockItem->product_location_picture) {
                \Storage::disk('public_uploads')->delete($stockItem->product_location_picture);
            }
        }

        if($request->hasFile('main_location_picture')) {
            $mainLink = $request->file('main_location_picture')
                ->store($folder, 'public_uploads');

            \Storage::disk('public_uploads')->delete($stockItem->main_location_picture);
            $stockItem->main_location_picture = '/' . $mainLink;
            $stockItem->save();
        }else{
            //Check if value has changed -> delete file
            if($request->main_location_picture != $stockItem->main_location_picture) {
                \Storage::disk('public_uploads')->delete($stockItem->main_location_picture);
            }
        }

        if($request->hasFile('secondary_location_picture')) {
            $secondaryLink = $request->file('secondary_location_picture')
                ->store($folder, 'public_uploads');
            \Storage::disk('public_uploads')->delete($stockItem->secondary_location_picture);
            $stockItem->secondary_location_picture = '/' . $secondaryLink;
            $stockItem->save();
        }else{
            //Check if value has changed -> delete file
            if($request->secondary_location_picture != $stockItem->secondary_location_picture) {
                \Storage::disk('public_uploads')->delete($stockItem->secondary_location_picture);
            }
        }

        if($request->hasFile('other_location_picture')) {
            $otherLink = $request->file('other_location_picture')
                ->store($folder, 'public_uploads');
            \Storage::disk('public_uploads')->delete($stockItem->other_location_picture);
            $stockItem->other_location_picture = '/' . $otherLink;
            $stockItem->save();
        }else{
            //Check if value has changed -> delete file
            if($request->other_location_picture != $stockItem->other_location_picture) {
                \Storage::disk('public_uploads')->delete($stockItem->other_location_picture);
            }
        }

        return response()->json(['message' => 'Stock Item successfully updated!']);
    }

    /**
     * Change Picture on Stock Item
     *
     * @param  mixed $request
     * @param  mixed $stockItem
     * @return response
     */
    public function changePicture(StockItemImageRequest $request, StockItems $stockItem)
    {
        $folder = "/uploads/images/stocks";

        if($request->hasFile('product_picture')) {
            $productLink = $request->file('product_picture')
                ->store($folder, 'public_uploads');
            \Storage::disk('public_uploads')->delete($stockItem->product_picture);
            $stockItem->product_picture = '/' . $productLink;
            $stockItem->save();

            return response()->json([
                'product_picture' => $stockItem->product_picture,
                'message' => 'Product Picture successfully saved!'
            ]);
        }
        if($request->hasFile('main_location_picture')) {
            $mainLink = $request->file('main_location_picture')
                ->store($folder, 'public_uploads');
            \Storage::disk('public_uploads')->delete($stockItem->main_location_picture);
            $stockItem->main_location_picture = '/' . $mainLink;
            $stockItem->save();

            return response()->json([
                'main_location_picture' => $stockItem->main_location_picture,
                'message' => 'Main Location Picture successfully saved!'
            ]);
        }
        if($request->hasFile('secondary_location_picture')) {
            $secondaryLink = $request->file('secondary_location_picture')
                ->store($folder, 'public_uploads');
            \Storage::disk('public_uploads')->delete($stockItem->secondary_location_picture);
            $stockItem->secondary_location_picture = '/' . $secondaryLink;
            $stockItem->save();

            return response()->json([
                'secondary_location_picture' => $stockItem->secondary_location_picture,
                'message' => 'Secondary Picture successfully saved!'
            ]);
        }
        if($request->hasFile('other_location_picture')) {
            $otherLink = $request->file('other_location_picture')
                ->store($folder, 'public_uploads');
            \Storage::disk('public_uploads')->delete($stockItem->other_location_picture);
            $stockItem->other_location_picture = '/' . $otherLink;
            $stockItem->save();

            return response()->json([
                'other_location_picture' => $stockItem->other_location_picture,
                'message' => 'Other Location successfully saved!'
            ]);
        }

    }

    /**
     * Remove Picture
     *
     * @param  mixed $request
     * @param  mixed $stockItem
     * @return void
     */
    public function removePicture(Request $request, StockItems $stockItem)
    {
        Log::info($request->all());
        if($request->field == 'product') {
            $stockItem->product_picture = null;
            $stockItem->save();
            \Storage::disk('public_uploads')->delete($stockItem->product_picture);
            return response()->json([
                'product_picture' => $stockItem->product_picture,
                'message' => 'Successfully Deleted!'
            ]);
        }
        if($request->field == 'main') {
            $stockItem->main_location_picture = null;
            $stockItem->save();
            \Storage::disk('public_uploads')->delete($stockItem->main_location_picture);
            return response()->json([
                'main_location_picture' => $stockItem->main_location_picture,
                'message' => 'Successfully Deleted!'
            ]);
        }
        if($request->field == 'secondary') {
            $stockItem->secondary_location_picture = null;
            $stockItem->save();
            \Storage::disk('public_uploads')->delete($stockItem->secondary_location_picture);
            return response()->json([
                'secondary_location_picture' => $stockItem->secondary_location_picture,
                'message' => 'Successfully Deleted!'
            ]);
        }
        if($request->field == 'other') {
            $stockItem->other_location_picture = null;
            $stockItem->save();
            \Storage::disk('public_uploads')->delete($stockItem->other_location_picture);
            return response()->json([
                'other_location_picture' => $stockItem->other_location_picture,
                'message' => 'Successfully Deleted!'
            ]);
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
