<?php
Route::group(['prefix' => 'exports', 'as' => 'exports.', 'namespace' => 'Exports'], function () {
    // work analytics
    Route::post('/work-analytics/export-hourly-work-analytics-report', 'WorkAnalyticsReportExportController@exportHourlyWorkAnalyticsReport')->name('work-analytics.export-hourly-work-analytics-report');
    Route::post('/work-analytics/export-daily-work-analytics-report', 'WorkAnalyticsReportExportController@exportDailyWorkAnalyticsReport')->name('work-analytics.export-daily-work-analytics-report');

    // raw scanners data
    Route::get('/export-raw-scanners-data', 'ExportController@exportRawScannersData')->name('data-export.export-raw-scanners-data');
});