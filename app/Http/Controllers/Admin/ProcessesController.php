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
use Gate;
use Illuminate\Support\Facades\DB;
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

    public function create()
    {
        abort_if(Gate::denies('process_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $processCategories = ProcessCategory::get();

        return view('admin.processes.create', compact('processCategories'));
    }

    public function store(StoreProcessRequest $request)
    {
        $processCategories = $request->get('process_categories');

        $process = new Process;
        $process->name = $request->get('name');
        $process->barcode = $request->get('barcode');
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

        return redirect()->route('admin.processes.index');
    }

    public function edit(Process $process)
    {
        abort_if(Gate::denies('process_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $processCategories = ProcessCategory::get();

        return view('admin.processes.edit', compact('process', 'processCategories'));
    }

    public function update(UpdateProcessRequest $request, Process $process)
    {
        $processCategories = $request->get('process_categories', []);

        $process->name = $request->get('name');
        $process->barcode = $request->get('barcode');
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

        return redirect()->route('admin.processes.index');
    }

    public function show(Process $process)
    {
        abort_if(Gate::denies('process_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $processCategories = ProcessCategory::get();

        $process = $process->loadMissing('processCategories');

        return view('admin.processes.show', compact('process', 'processCategories'));
    }

    public function destroy(Process $process)
    {
        abort_if(Gate::denies('process_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $process->delete();

        return back();
    }

    public function massDestroy(MassDestroyProcessRequest $request)
    {
        Process::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
