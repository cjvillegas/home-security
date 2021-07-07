<?php
Route::group(['prefix' => 'reports', 'as' => 'reports.', 'namespace' => 'Report'], function () {
    Route::get('/work-analytics/get-hourly-analytics', 'WorkAnalyticsReportController@getHourlyAnalytics')->name('work-analytics.get-hourly-analytics');
    Route::resource('work-analytics', 'WorkAnalyticsReportController');
});
