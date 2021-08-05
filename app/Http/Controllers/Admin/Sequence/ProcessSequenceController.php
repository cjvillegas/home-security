<?php

namespace App\Http\Controllers\Admin\Sequence;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessSequence\StoreProcessSequence;
use App\Http\Requests\ProcessSequence\UpdateProcessSequence;
use App\Models\Process;
use App\Models\ProcessSequence\ProcessSequence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProcessSequenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.process-sequence.index');
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

        $processSequences = ProcessSequence::orderBy('created_at', 'desc')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('name', 'like', "%{$searchString}%");
            });

        $processSequences = $processSequences->paginate($size);

        return response()->json($processSequences);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreProcessSequence  $request
     *
     * @return JsonResponse
     */
    public function store(StoreProcessSequence $request)
    {
        $attributes = $request->all();
        $attributes['created_by'] = auth()->user()->id;

        $model = new ProcessSequence();
        $model->fill($attributes);
        $saved = $model->save();

        return response()->json($saved);
    }

    /**
     * Display the specified resource.
     *
     * @param  ProcessSequence  $processSequence
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|JsonResponse
     */
    public function show(ProcessSequence $processSequence)
    {
        $processSequence = ProcessSequence::findOrFail($processSequence->id);

        // if the request is from ajax and is expecting a json response
        if (request()->ajax()) {
            return response()->json($processSequence);
        }

        $processes = Process::get();

        return view('admin.process-sequence.show', compact('processSequence', 'processes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProcessSequence  $request
     * @param  ProcessSequence  $processSequence
     *
     * @return JsonResponse
     */
    public function update(UpdateProcessSequence $request, ProcessSequence $processSequence)
    {
        $processSequence->updated_by = auth()->user()->id;
        $processSequence->name = $request->get('name');

        $saved = $processSequence->save();

        return response()->json($saved);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ProcessSequence $processSequence
     *
     * @return JsonResponse
     */
    public function destroy(ProcessSequence $processSequence)
    {
        $deleted = $processSequence->delete();

        if ($deleted) {
            // modify the deleted name so that it will not cause conflict
            // to the newly created process category.
            // This is useful because the name column is unique in the DB level and we are only soft deleting
            // data in the process_sequences table
            $now = now()->unix();
            $processSequence->name = "{$processSequence->name}_deleted_{$now}";
            $processSequence->save();
        }

        return response()->json($deleted);
    }
}
