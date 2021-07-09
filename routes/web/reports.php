<?php
Route::group(['prefix' => 'reports', 'as' => 'reports.', 'namespace' => 'Report'], function () {
    Route::get('/work-analytics/get-hourly-analytics', 'WorkAnalyticsReportController@getHourlyAnalytics')->name('work-analytics.get-hourly-analytics');
    Route::get('/work-analytics/get-daily-analytics', 'WorkAnalyticsReportController@getDailyAnalytics')->name('work-analytics.get-daily-analytics');
    Route::get('/work-analytics/manufactured-blinds-analytics', 'WorkAnalyticsReportController@manufacturedBlindsAnalytics')->name('home');
    Route::resource('work-analytics', 'WorkAnalyticsReportController');
});
