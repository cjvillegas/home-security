<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exports\ExportRawScannersDataRequest;
use App\Models\Export;
use App\Models\User;
use App\Services\CsvExporterService;
use App\Services\Reports\ScannersDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    /**
     * Return exports index page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('download_export_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.exports.index');
    }

    /**
     * @param ExportRawScannersDataRequest $request
     *
     * @return JsonResponse
     */
    public function exportRawScannersData(ExportRawScannersDataRequest $request): JsonResponse
    {
        $user = User::find(auth()->user()->id);

        $service = new ScannersDataService($request->all());

        $exporter = new CsvExporterService($user);
        $exporter->setName('Raw Scanners Data')
            ->setPath('exports')
            ->setHeaders([
                'order_no' => 'Order No.',
                'order_at' => 'Ordered At',
                'customer' => 'Customer',
                'blind_type' => 'Blind Type',
                'quantity' => 'Quantity',
                'serial_id' => 'Serial ID',
                'name' => 'Operation',
                'fullname' => 'Employee',
                'scanned_date_time' => 'Scanned Date',
                'machine_name' => 'Machine Name'
            ])
            ->export($service);

        return response()->json([
            'message' => 'Your data is being exported. Please wait a while and check the Export page for your export.',
            'success' => true
        ]);
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
