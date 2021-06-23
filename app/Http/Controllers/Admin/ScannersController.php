<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyScannerRequest;
use App\Http\Requests\StoreScannerRequest;
use App\Http\Requests\UpdateScannerRequest;
use App\Models\Scanner;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScannersController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('scanner_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $scanners = Scanner::all();

        return view('admin.scanners.index', compact('scanners'));
    }

    public function create()
    {
        abort_if(Gate::denies('scanner_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.scanners.create');
    }

    public function store(StoreScannerRequest $request)
    {
        $scanner = Scanner::create($request->all());

        return redirect()->route('admin.scanners.index');
    }

    public function edit(Scanner $scanner)
    {
        abort_if(Gate::denies('scanner_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.scanners.edit', compact('scanner'));
    }

    public function update(UpdateScannerRequest $request, Scanner $scanner)
    {
        $scanner->update($request->all());

        return redirect()->route('admin.scanners.index');
    }

    public function show(Scanner $scanner)
    {
        abort_if(Gate::denies('scanner_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.scanners.show', compact('scanner'));
    }

    public function destroy(Scanner $scanner)
    {
        abort_if(Gate::denies('scanner_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $scanner->delete();

        return back();
    }

    public function massDestroy(MassDestroyScannerRequest $request)
    {
        Scanner::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
