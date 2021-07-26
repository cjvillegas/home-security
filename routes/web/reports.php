<?php
Route::group(['prefix' => 'reports', 'as' => 'reports.', 'namespace' => 'Report'], function () {
    // work analytics
    Route::group(['prefix' => 'work-analytics', 'as' => 'work-analytics.'], function () {
        Route::get('/get-work-analytics', 'WorkAnalyticsReportController@getWorkAnalytics')->name('get-work-analytics');
        Route::get('/manufactured-blinds-analytics', 'WorkAnalyticsReportController@manufacturedBlindsAnalytics')->name('manufactured-blinds-analytics');
        Route::get('/despatch-department-analytics', 'WorkAnalyticsReportController@getDespatchDepartmentAnalytics')->name('despatch-department-analytics');
        Route::resource('', 'WorkAnalyticsReportController')->only(['index']);
    });


    Route::get('/data-export', 'ReportController@dataExport')->name('data-export.index');
});
