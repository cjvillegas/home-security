<?php

Route::namespace('QualityControl')->as('quality-control.')->prefix('quality-control')->group( function() {
    Route::get('/', 'QualityControlController@index')->name('index');
    Route::post('/list', 'QualityControlController@fetchQualityControls');
    Route::post('/store', 'QualityControlController@store');
    Route::patch('/{qualityControl}/update', 'QualityControlController@update');
    Route::delete('/{qualityControl}/destroy', 'QualityControlController@destroy');
});
