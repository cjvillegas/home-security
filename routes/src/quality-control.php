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
});

