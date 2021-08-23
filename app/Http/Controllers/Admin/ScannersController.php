<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyScannerRequest;
use App\Http\Requests\Scanner\StoreQcTag;
use App\Http\Requests\Scanner\UpdateQcTag;
use App\Http\Requests\StoreScannerRequest;
use App\Http\Requests\UpdateScannerRequest;
use App\Models\QcFault;
use App\Models\Scanner;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScannersController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('scanner_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.scanners.index');
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

    /**
     * Fetch list of scanners
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function fetchScanners(Request $request)
    {
        $search = $request->input('search.value'); // used for searching
        $draw = $request->get('draw'); // used in Datatable
        $limit = $request->get('length'); // used for pagination as limit
        $offset = $request->get('start'); // used for pagination as offset
        $index = $request->input('order.0.column'); // used for ordering, define the column  to order
        $direction = $request->input('order.0.dir'); // used for ordering, define the direction of the order
        $column = $request->input('columns')[$index]['name']; // used for ordering, the column to order

        $scanners = Scanner::orderBy($column, $direction);

        // if a search string exists
        if ($search) {
            $scanners->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%");
                $q->orWhere('scannedtime', 'like', "%$search%");
                $q->orWhere('employeeid', 'like', "%$search%");
                $q->orWhere('processid', 'like', "%$search%");
                $q->orWhere('blindid', 'like', "%$search%");
            });
        }

        // retrieved filtered count
        $filteredCount = $scanners->count();

        // return the actual viewable results
        $scanners = $scanners->limit($limit)->offset($offset)->get();

        return response()->json([
            'data' => $scanners,
            'recordsTotal' => Scanner::count(),
            'recordsFiltered' => $filteredCount,
            'draw' => $draw,
        ]);
    }

    /**
     * Searches scanners based on the passed field name
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchScannersByField(Request $request)
    {
        $field = $request->get('field');
        $searchString = $request->get('searchString');

        if (!$field || !$searchString) {
            return response()->json([]);
        }

        // do the actual query
        $scanners = Scanner::where($field, 'like', "%$searchString%")
            ->limit(25)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        return response()->json($scanners);
    }

    /**
     * Retrieve all scanners based on the passed field name
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getScannersByField(Request $request)
    {
        $field = $request->get('field');
        $toSearch = $request->get('toSearch');

        if (!$field || !$toSearch) {
            return response()->json([]);
        }

        // do the actual query
        $scanners = Scanner::where($field, 'like', "%$toSearch%")
            ->with(['employee' => function ($query) {
                $query->withTrashed()->with(['shift', 'team']);
            }, 'process', 'order', 'qcFault'])
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        return response()->json($scanners);
    }

    /**
     * Creates new qc tag
     *
     * @param StoreQcTag $request
     *
     * @return JsonResponse
     */
    public function qcTag(StoreQcTag $request)
    {
        $qcTag = new QcFault();
        $qcTag->fill($request->all());
        $qcTag->operation_date = date('Y-m-d H:i:s', strtotime($request->get('operation_date')));
        $qcTag->save();

        return response()->json($qcTag);
    }

    /**
     * Updates a qc tag
     *
     * @param QcFault $qcFault
     *
     * @return JsonResponse
     */
    public function updateQcTag(UpdateQcTag $request, QcFault $qcFault)
    {
        $qcFault->fill($request->all());
        $qcFault->save();

        return response()->json($qcFault->refresh());
    }

    /**
     * Deletes a qc tag
     *
     * @param QcFault $qcFault
     *
     * @return JsonResponse
     */
    public function removeQcTag(QcFault $qcFault)
    {
        // checks if the current user has the authorization to delete a qc tag
        if (!Gate::allows('qc_tag_delete')) {
            abort(401);
        }

        $qcFault->delete();

        return response()->json($qcFault->refresh());
    }
}
