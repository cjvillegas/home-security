<?php
Route::group(['prefix' => 'reports', 'as' => 'reports.', 'namespace' => 'Report'], function () {
    // work analytics
    Route::group(['prefix' => 'work-analytics', 'as' => 'work-analytics.'], function () {
        Route::get('/get-work-analytics', 'WorkAnalyticsReportController@getWorkAnalytics')->name('get-work-analytics');
        Route::get('/manufactured-blinds-analytics', 'WorkAnalyticsReportController@manufacturedBlindsAnalytics')->name('manufactured-blinds-analytics');
        Route::get('/despatch-department-analytics', 'WorkAnalyticsReportController@getDespatchDepartmentAnalytics')->name('despatch-department-analytics');
        Route::resource('', 'WorkAnalyticsReportController')->only(['index']);
    });

    Route::get('/dashboard-machine-statistics', 'ReportController@getMachineStatistics');
    Route::get('/data-export', 'ReportController@dataExport')->name('data-export.index');
    Route::get('/qc-report', 'ReportController@qcReport')->name('qc-report');
    Route::get('/qc-list', 'ReportController@getQcList')->name('qc-list');
    Route::get('/export-qc-fault-data', 'ReportController@exportQcFaultData')->name('export-qc-fault-data');

    Route::get('/team-status', 'ReportController@teamStatus')->name('team-status');
    Route::get('/team-status-list', 'ReportController@getTeamStatusReport')->name('team-status-list');
    Route::get('/export-team-status-report', 'ReportController@exportTeamStatus')->name('export-team-status-report');

    //fire register
    Route::get('/fire-register', 'FireRegisterController@fireRegister')->name('fire-register');
    Route::post('/fire-register', 'FireRegisterController@getEmployees')->name('fire-register.get-employees');

    //manufacture blinds
    Route::get('/manufactured-blinds', 'ManufacturedBlindController@index')->name('manufactured-blinds');
    Route::post('/manufactured-blinds', 'ManufacturedBlindController@getBlinds')->name('manufactured-blinds');
    // time & attendance report
    Route::get('/time-and-attendance-page', 'ReportController@timeAndAttendancePage')->name('time-and-attendance-page');
    Route::get('/time-and-attendance', 'ReportController@timeAndAttendance')->name('time-and-attendance');
    Route::get('/export-time-and-attendance', 'ReportController@exportTimeAndAttendance')->name('export-time-and-attendance');

    // who works here
    Route::get('/who-works-here-page', 'ReportController@whoWorksHerePage')->name('who-works-here-page');
    Route::get('/who-works-here', 'ReportController@whoWorksHere')->name('who-works-here');

    // dashboard machine statistics
    Route::get('/dashboard/machine-statistics', 'ReportController@dashboardMachineStatistics')->name('dashboard-machine-statistics');
});
