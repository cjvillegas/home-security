<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Navigate to the Data Export page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dataExport()
    {
        abort_if(Gate::denies('data_export_reports_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.reports.data-export-index');
    }
}
