<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProcessCategory\StoreProcessCategory;
use App\Http\Requests\Settings\ProcessCategory\UpdateProcessCategory;
use App\Models\ProcessCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProcessCategoryController extends Controller
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

        return view('admin.process-category.index', compact('user'));
    }

    /**
     * Fetch list of process categories
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $searchString = $request->get('searchString');
        $size = $request->get('size');

        $processCategories = ProcessCategory::orderBy('created_at', 'desc')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('code', 'like', "%{$searchString}%")
                    ->orWhere('name', 'like', "%{$searchString}%");
            });

        $processCategories = $processCategories->paginate($size);

        return response()->json($processCategories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreProcessCategory  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProcessCategory $request)
    {
        $attributes = $request->all();
        $attributes['created_by'] = auth()->user()->id;

        $model = new ProcessCategory();
        $model->fill($attributes);
        $saved = $model->save();

        return response()->json($saved);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProcessCategory  $request
     * @param  ProcessCategory  $processCategory
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProcessCategory $request, ProcessCategory $processCategory)
    {
        $processCategory->updated_by = auth()->user()->id;
        $processCategory->code = $request->get('code');
        $processCategory->name = $request->get('name');

        $saved = $processCategory->save();

        return response()->json($saved);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ProcessCategory  $processCategory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProcessCategory $processCategory)
    {
        // checks if the current user has the authorization to create a
        // new process category
        if (!Gate::allows('process_categories_delete')) {
            abort(401);
        }

        $deleted = $processCategory->delete();

        if ($deleted) {
            // modify the deleted code so that it will not cause conflict
            // to the newly created process category.
            // This is useful because the code column is unique in the DB level and we are only soft deleting
            // data in the process_categories table
            $now = now()->unix();
            $processCategory->code = "{$processCategory->code}_deleted_{$now}";
            $processCategory->save();
        }

        return response()->json($deleted);
    }
}
