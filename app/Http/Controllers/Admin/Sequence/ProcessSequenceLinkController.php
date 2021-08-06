<?php

namespace App\Http\Controllers\Admin\Sequence;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessSequence\StoreProcessSequenceLink;
use App\Models\ProcessSequence\ProcessSequence;
use App\Models\ProcessSequence\ProcessSequenceLink;
use App\Services\Factories\ProcessSequenceFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProcessSequenceLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProcessSequence $processSequence
     *
     * @return JsonResponse
     */
    public function index(ProcessSequence $processSequence)
    {
        $steps = $processSequence->steps()->sortedSteps()->with(['process'])->get();

        return response()->json($steps);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreProcessSequenceLink  $request
     *
     * @return JsonResponse
     */
    public function store(StoreProcessSequenceLink $request)
    {
        $factory = new ProcessSequenceFactory();
        $link = $factory->createNewStep($request);

        return response()->json($link);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ProcessSequenceLink $processSequenceLink
     *
     * @return JsonResponse
     */
    public function destroy(ProcessSequence $processSequence, ProcessSequenceLink $processSequenceLink)
    {
        $deleted = $processSequenceLink->delete();

        // make sure that the step is deleted before updating the parent process sequence
        if ($deleted) {
            $processSequence->updated_at = now();
            $processSequence->save();

            $factory = new ProcessSequenceFactory();
            $factory->adjustStepsOrder($processSequence->steps->first());

            return response()->json(true);
        }

        return response()->json(false);
    }

    /**
     * Modifies the sequence step orders
     *
     * @param Request $request
     * @param ProcessSequence $processSequence
     * @param ProcessSequenceLink $processSequenceLink
     *
     * @return JsonResponse
     */
    public function moveStepOrder(Request $request, ProcessSequence $processSequence, ProcessSequenceLink $processSequenceLink)
    {
        $factory = new ProcessSequenceFactory();
        $steps = $factory->adjustStepsOrder($processSequenceLink, $request->get('direction'))
            ->loadMissing(['process']);

        return response()->json($steps);
    }
}
