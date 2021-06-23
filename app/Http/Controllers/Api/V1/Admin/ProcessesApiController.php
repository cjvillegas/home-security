<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProcessRequest;
use App\Http\Requests\UpdateProcessRequest;
use App\Http\Resources\Admin\ProcessResource;
use App\Models\Process;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('process_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProcessResource(Process::all());
    }

    public function store(StoreProcessRequest $request)
    {
        $process = Process::create($request->all());

        return (new ProcessResource($process))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Process $process)
    {
        abort_if(Gate::denies('process_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProcessResource($process);
    }

    public function update(UpdateProcessRequest $request, Process $process)
    {
        $process->update($request->all());

        return (new ProcessResource($process))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Process $process)
    {
        abort_if(Gate::denies('process_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $process->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
