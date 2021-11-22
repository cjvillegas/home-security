<?php

Route::namespace('QualityControl')->as('quality-control.')->prefix('quality-control')->group( function() {
    Route::get('/', 'QualityControlController@index')->name('index');
    Route::match(['post', 'get'], '/list', 'QualityControlController@fetchQualityControls');
    Route::post('/store', 'QualityControlController@store');
    Route::patch('/{qualityControl}/update', 'QualityControlController@update');
    Route::delete('/{qualityControl}/destroy', 'QualityControlController@destroy');
});
Route::namespace('QualityControl')->group(function() {
    Route::get('remake-checker', 'QcRemakeCheckerController@index')->name('remake-checker.index');
    Route::post('remake-checker/get-orders', 'QcRemakeCheckerController@getOrders')->name('remake-checker.get-orders');
    Route::post('remake-checker', 'QcRemakeCheckerController@storeOrderRemakeChecker')->name('remake-checker.store');

    //remake reports
    Route::get('remake-report', 'QcRemakeCheckerController@orderRemakeReport')->name('remake-checker.report');
    Route::post('remake-report/get-list', 'QcRemakeCheckerController@getOrderRemake');

    //email notifications
    Route::get('email', 'QcRemakeCheckerController@orderRemakeEmailNotification')->name('remake-checker.email');
    Route::post('email/get-list', 'QcRemakeCheckerController@getEmails');
    Route::post('email/store', 'QcRemakeCheckerController@storeEmail');
    Route::delete('{qcEmail}/destroy', 'QcRemakeCheckerController@deleteEmail');
});

