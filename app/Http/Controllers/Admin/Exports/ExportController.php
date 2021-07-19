<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Exports\RawScannersDataExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportRawScannersData(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        return new RawScannersDataExport($start, $end);
    }
}
