<?php

Route::namespace('InHouse')->as('in-house.')->prefix('in-house')->group( function() {
    Route::post('stocks/list', 'StockItemController@fetchStocks');
    Route::apiResource('stocks', 'StockItemController')->only(['index']);
});
