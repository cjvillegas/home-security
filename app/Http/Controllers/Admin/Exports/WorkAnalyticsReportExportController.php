<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Exports\Reports\WorkAnalytics\DailyWorkAnalyticsReportExport;
use App\Exports\Reports\WorkAnalytics\HourlyWorkAnalyticsReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkAnalyticsReportExportController extends Controller
{
    /**
     * Exports hourly report analytics to a CSV file
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportHourlyWorkAnalyticsReport(Request $request)
    {
        $export = new HourlyWorkAnalyticsReportExport($request->get('headers'), $request->get('data'));

        return $export->download('Hourly Work Analytics Report.xlsx');
    }

    /**
     * Exports daily report analytics to a CSV file
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportDailyWorkAnalyticsReport(Request $request)
    {
        $export = new DailyWorkAnalyticsReportExport($request->get('headers'), $request->get('data'));

        return $export->download('Daily Work Analytics Report.xlsx');
    }
}
