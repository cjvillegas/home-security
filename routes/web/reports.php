<?php
Route::group(['prefix' => 'reports', 'as' => 'reports.', 'namespace' => 'Report'], function () {
    // work analytics
    Route::get('/work-analytics/get-work-analytics', 'WorkAnalyticsReportController@getWorkAnalytics')->name('work-analytics.get-work-analytics');
    Route::get('/work-analytics/manufactured-blinds-analytics', 'WorkAnalyticsReportController@manufacturedBlindsAnalytics')->name('home');
    Route::resource('work-analytics', 'WorkAnalyticsReportController');

    Route::get('/data-export', 'ReportController@dataExport')->name('data-export.index');
});
