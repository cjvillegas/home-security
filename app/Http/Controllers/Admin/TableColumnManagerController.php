<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableColumnManager\GetTableColumnManagerByTypeRequest;
use App\Http\Requests\TableColumnManager\TableColumnManagerRequest;
use App\Models\TableColumnManager;
use Illuminate\Http\JsonResponse;

class TableColumnManagerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  TableColumnManagerRequest $request
     * @return JsonResponse
     */
    public function store(TableColumnManagerRequest $request): JsonResponse
    {
        $filter = new TableColumnManager();
        $filter->user_id = auth()->user()->id;
        $filter->type = $request->get('type');
        $filter->columns = $request->get('columns');
        $filter->save();

        return response()->json($filter);
    }

    /**
     * Display the specified resource.
     *
     * @param  TableColumnManager $tableColumnManager
     *
     * @return JsonResponse
     */
    public function show(TableColumnManager $tableColumnManager): JsonResponse
    {
        return response()->json($tableColumnManager);
    }

    /**
     * Get a filter instance base on the given type
     *
     * @param  GetTableColumnManagerByTypeRequest $request
     *
     * @return JsonResponse
     */
    public function getByType(GetTableColumnManagerByTypeRequest $request): JsonResponse
    {
        $tableColumnManager = TableColumnManager::where('type', $request->get('type'))
            ->where('user_id', auth()->user()->id)
            ->first();

        return response()->json([
            'message' => 'Successfully fetched table column manager by type.',
            'data' => $tableColumnManager
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TableColumnManager $tableColumnManager
     * @param  TableColumnManagerRequest $request
     *
     * @return JsonResponse
     */
    public function update(TableColumnManagerRequest $request, TableColumnManager $tableColumnManager): JsonResponse
    {
        $tableColumnManager->columns = $request->has('columns') ? $request->get('columns') : $tableColumnManager->columns;
        $tableColumnManager->save();

        return response()->json($tableColumnManager->refresh());
    }
}
