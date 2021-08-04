<?php

Route::namespace('InHouse')->as('in-house.')->prefix('in-house')->group( function() {
    Route::post('stocks/list', 'StockItemController@fetchStocks');
    Route::get('stocks/{stockItem}/show', 'StockItemController@show');
    Route::post('stocks/{stockItem}/update', 'StockItemController@update');
    // Route::post('stocks/{stockItem}/changePicture', 'StockItemController@changePicture');
    // Route::post('stocks/{stockItem}/removePhoto', 'StockItemController@removePicture');
    Route::delete('stocks/{stockItem}/destroy', 'StockItemController@destroy');
    Route::apiResource('stocks', 'StockItemController')->only(['index', 'store']);
});
