<?php

Route::namespace('InHouse')->as('in-house.')->prefix('in-house')->group( function() {
    Route::post('stocks/list', 'StockItemController@fetchStocks');
    Route::get('stocks/{stockItem}/show', 'StockItemController@show');
    Route::post('stocks/{stockItem}/update', 'StockItemController@update');
    Route::delete('stocks/{stockItem}/destroy', 'StockItemController@destroy');
    Route::apiResource('stocks', 'StockItemController')->only(['index', 'store']);

    Route::get('stock-levels', 'StockLevelController@index')->name('stocklevels.index');
    Route::get('stock-levels/last-sync', 'StockLevelController@lastSync');
    Route::post('stock-levels/list', 'StockLevelController@fetchStockLevels');
});
