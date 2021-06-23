<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScannerRequest;
use App\Http\Requests\UpdateScannerRequest;
use App\Http\Resources\Admin\ScannerResource;
use App\Models\Scanner;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScannersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('scanner_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ScannerResource(Scanner::all());
    }

    public function store(StoreScannerRequest $request)
    {
        $scanner = Scanner::create($request->all());

        return (new ScannerResource($scanner))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Scanner $scanner)
    {
        abort_if(Gate::denies('scanner_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ScannerResource($scanner);
    }

    public function update(UpdateScannerRequest $request, Scanner $scanner)
    {
        $scanner->update($request->all());

        return (new ScannerResource($scanner))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Scanner $scanner)
    {
        abort_if(Gate::denies('scanner_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $scanner->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
