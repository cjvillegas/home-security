<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Filter\GetFilterByTypeRequest;
use App\Http\Requests\Filter\FilterRequest;
use App\Models\Filter;
use Illuminate\Http\JsonResponse;

class FilterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  FilterRequest $request
     *
     * @return JsonResponse
     */
    public function store(FilterRequest $request): JsonResponse
    {
        $filter = new Filter();
        $filter->user_id = auth()->user()->id;
        $filter->type = $request->get('type');
        $filter->filters = $request->get('filters');
        $filter->name = $request->get('name');
        $filter->save();

        return response()->json($filter);
    }

    /**
     * Display the specified resource.
     *
     * @param  Filter $filter
     *
     * @return JsonResponse
     */
    public function show(Filter $filter): JsonResponse
    {
        return response()->json($filter);
    }

    /**
     * Get a filter instance base on the given type
     *
     * @param  GetFilterByTypeRequest $request
     *
     * @return JsonResponse
     */
    public function getByType(GetFilterByTypeRequest $request): JsonResponse
    {
        $filter = Filter::where('type', $request->get('type'))
            ->where('user_id', auth()->user()->id)
            ->first();

        return response()->json($filter);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FilterRequest $request
     * @param  Filter $filter
     *
     * @return JsonResponse
     */
    public function update(FilterRequest $request, Filter $filter): JsonResponse
    {
        $filter->name = $request->has('name') ? $request->get('name') : $filter->name;
        $filter->filters = $request->has('filters') ? $request->get('filters') : $filter->filters;
        $filter->save();

        return response()->json($filter->refresh());
    }
}
