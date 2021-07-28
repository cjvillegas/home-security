<?php

Route::namespace('InHouse')->as('in-house.')->prefix('in-house')->group( function() {
    Route::apiResource('stocks', 'StockItemController')->only(['index']);
});
