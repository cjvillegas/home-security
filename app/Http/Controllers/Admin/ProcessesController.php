<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyProcessRequest;
use App\Http\Requests\StoreProcessRequest;
use App\Http\Requests\UpdateProcessRequest;
use App\Models\CategoryProcess;
use App\Models\Process;
use App\Models\ProcessCategory;
use App\Models\ProcessSequence\ProcessSequence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ProcessesController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('process_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $processes = Process::with(['processCategories'])->get();

        return view('admin.processes.index', compact('processes'));
    }

    /**
     * @param StoreProcessRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreProcessRequest $request)
    {
        $processCategories = $request->get('process_categories');

        $process = new Process;
        $process->name = $request->get('name');
        $process->barcode = $request->get('barcode');
        $process->trade_target = $request->get('trade_target', $process->trade_target);
        $process->internet_target = $request->get('internet_target', $process->internet_target);
        $process->trade_target_new_joiner = $request->get('trade_target_new_joiner', $process->trade_target_new_joiner);
        $process->internet_target_new_joiner = $request->get('internet_target_new_joiner', $process->internet_target_new_joiner);
        $process->process_manufacturing_time = $request->get('process_manufacturing_time');
        $process->stop_start_button_required = $request->get('stop_start_button_required');
        $process->team_trade_target = $request->get('team_trade_target');
        $process->team_internet_target = $request->get('team_internet_target');
        $saved = $process->save();

        // if a new process is successfully added and process categories are present
        // add them, it's obvious
        if ($saved && !empty($processCategories)) {
            $categoryProcesses = [];

            // build the data for mass insertion
            foreach ($processCategories as $processCategory) {
                $categoryProcesses[] = ['process_id' => $process->id, 'process_category_id' => $processCategory];
            }

            // do the actual insertion here
            CategoryProcess::insert($categoryProcesses);
        }

        return response()->json(true);
    }

    public function edit(Process $process)
    {
        abort_if(Gate::denies('process_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $processCategories = ProcessCategory::get();

        return view('admin.processes.edit', compact('process', 'processCategories'));
    }

    /**
     * @param UpdateProcessRequest $request
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function update(UpdateProcessRequest $request, Process $process)
    {
        $processCategories = $request->get('process_categories', []);

        $process->name = $request->get('name');
        $process->barcode = $request->get('barcode');
        $process->trade_target = $request->get('trade_target', $process->trade_target);
        $process->internet_target = $request->get('internet_target', $process->internet_target);
        $process->trade_target_new_joiner = $request->get('trade_target_new_joiner', $process->trade_target_new_joiner);
        $process->internet_target_new_joiner = $request->get('internet_target_new_joiner', $process->internet_target_new_joiner);
        $process->process_manufacturing_time = $request->get('process_manufacturing_time', $process->process_manufacturing_time);
        $process->stop_start_button_required = $request->get('stop_start_button_required', $process->stop_start_button_required);
        $process->team_trade_target = $request->get('team_trade_target', $process->team_trade_target);
        $process->team_internet_target = $request->get('team_internet_target', $process->team_internet_target);
        $saved = $process->save();

        if ($saved) {
            // remove process' process categories that is not present in the request
            DB::table('category_processes')->where('process_id', $process->id)
            ->whereNotIn('process_category_id', $processCategories)->delete();

            if (!empty($processCategories)) {
                $categoryProcesses = [];

                // build the data for mass insertion
                foreach ($processCategories as $processCategory) {
                    if (!$process->processCategories->contains($processCategory)) {
                        $categoryProcesses[] = ['process_id' => $process->id, 'process_category_id' => $processCategory];
                    }
                }

                // do the actual insertion here
                CategoryProcess::insert($categoryProcesses);
            }
        }

        return response()->json($process);
    }

    /**
     * Fetch single instance of a process
     *
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function show(Process $process)
    {
        abort_if(Gate::denies('process_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $process = $process->loadMissing('processCategories');

        return response()->json($process);
    }

    /**
     * @param Process $process
     *
     * @return JsonResponse
     */
    public function destroy(Process $process)
    {
        abort_if(Gate::denies('process_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $process->delete();

        return response()->json(true);
    }

    public function massDestroy(MassDestroyProcessRequest $request)
    {
        Process::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Return all not deleted processes
     *
     * @return JsonResponse
     */
    public function getAllProcesses()
    {
        return response()->json(Process::get());
    }

    /**
     * Fetch list of processes
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $searchString = $request->get('searchString');
        $size = $request->get('size');

        $processes = Process::orderBy('created_at', 'desc')
            ->with('processCategories')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('name', 'like', "%{$searchString}%");
                $query->orWhere('barcode', 'like', "%{$searchString}%");
            });

        $processes = $processes->paginate($size);

        return response()->json($processes);
    }

    /**
     * Fetch processes by Search
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function searchProcesses(Request $request): JsonResponse
    {
        $searchString = $request->get('searchString');

        $processes = ProcessSequence::orderBy('name', 'desc')
                ->when($searchString, function ($query) use ($searchString) {
                    $query->where('name', 'like', "%{$searchString}%");
                })->get();

        return response()->json(['processes' => $processes]);
    }

}
