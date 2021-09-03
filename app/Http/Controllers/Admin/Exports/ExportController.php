<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Exports\RawScannersDataExport;
use App\Http\Controllers\Controller;
use App\Models\Export;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    /**
     * Return exports index page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.exports.index');
    }

    /**
     * @param Request $request
     *
     * @return RawScannersDataExport
     */
    public function exportRawScannersData(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        return new RawScannersDataExport($start, $end);
    }

    /**
     * Retrieve list of exports
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getExports(Request $request)
    {
        $size = $request->get('size');

        $exports = Export::orderBy('created_at', 'desc')
            ->select([
                'exports.*',
                'u.name AS user_name'
            ])
            ->join('users AS u', 'u.id', 'exports.user_id');

        $exports = $exports->paginate($size);

        return response()->json($exports);
    }

    /**
     * Delete an export instance
     *
     * @param Export $export
     *
     * @return bool|null
     */
    public function delete(Export $export)
    {
        $folderPath = "exports/{$export->id}";

        Storage::deleteDirectory($folderPath);

        return $export->delete();
    }
}
