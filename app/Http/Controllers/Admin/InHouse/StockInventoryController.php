<?php

namespace App\Http\Controllers\Admin\InHouse;

use App\Http\Controllers\SbgBaseController;
use App\Interfaces\BaseFactoryInterface;
use App\Models\StockLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockInventoryController extends SbgBaseController
{
    /**
     * @var BaseFactoryInterface
     */
    private $factory;
    /**
     * StockInventoryController constructor
     *
     * @param BaseFactoryInterface
     */
    public function __construct(BaseFactoryInterface $stockOrderFactory)
    {
        $this->factory = $stockOrderFactory;

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.in-house.stock-inventory');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $order = $this->factory->newOrder($this->user);

        return response()->json($order);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchStockLevels(Request $request): JsonResponse
    {
        $searchString = $request->get('searchString');
        $toNeglectIds = $request->get('ids');

        $stockLevels = StockLevel::query()
            ->when(!empty($searchString), function ($query) use ($searchString) {
                $query->where('code', 'like', "%{$searchString}%");
            })
            ->when($toNeglectIds, function ($query) use ($toNeglectIds) {
                $query->whereNotIn('id', $toNeglectIds);
            })
            ->limit(25)
            ->orderBy('code')
            ->get();

        return response()->json($stockLevels);
    }
}
