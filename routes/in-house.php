<?php

Route::namespace('InHouse')->as('in-house.')->prefix('in-house')->group( function() {
    Route::post('stocks/list', 'StockItemController@fetchStocks');
    Route::get('stocks/{stockItem}/show', 'StockItemController@show');
    Route::post('stocks/{stockItem}/update', 'StockItemController@update');
    Route::delete('stocks/{stockItem}/destroy', 'StockItemController@destroy');
    Route::apiResource('stocks', 'StockItemController')->only(['index', 'store']);

    Route::get('stock-levels', 'StockLevelController@index')->name('stock-levels.index');
    Route::get('stock-levels/out-of-stock/total-count', 'StockLevelController@outOfStockTotalCount')->name('stock-levels.out-of-stock.total-count');
    Route::get('stock-levels/last-sync', 'StockLevelController@lastSync');
    Route::post('stock-levels/list', 'StockLevelController@fetchStockLevels');

    // Stock Inventory Routes
    Route::get('stock-orders/search-stock-levels', 'StockInventoryController@searchStockLevels')->name('stock-orders.search-stock-levels');
    Route::get('stock-orders/draft', 'StockInventoryController@draftOrders')->name('stock-orders.draft');
    Route::get('stock-orders/pending', 'StockInventoryController@pendingOrders')->name('stock-orders.pending');
    Route::get('stock-orders/approved', 'StockInventoryController@approvedOrders')->name('stock-orders.approved');
    Route::get('stock-orders/draft/total-count', 'StockInventoryController@countDraftOrders')->name('stock-orders.draft.total-count');
    Route::get('stock-orders/pending/total-count', 'StockInventoryController@countPendingOrders')->name('stock-orders.pending.total-count');
    Route::get('stock-orders/approved/total-count', 'StockInventoryController@countApprovedOrders')->name('stock-orders.approved.total-count');
    Route::patch('stock-orders/cancel/{stock_order}', 'StockInventoryController@cancelStockOrder')->name('stock-orders.cancel-order');
    Route::patch('stock-orders/approve/{stock_order}', 'StockInventoryController@approveOrder')->name('stock-orders.approve-order');
    Route::patch('stock-orders/clone/{stock_order}', 'StockInventoryController@cloneOrder')->name('stock-orders.clone-order');
    Route::resource('stock-orders', 'StockInventoryController')->except(['create']);

    // Stock Inventory Order Lines
    Route::post('stock-order-items/move', 'StockOrderItemController@moveItems')->name('stock-order-items.move');
    Route::post('stock-order-items/move-to-new-order', 'StockOrderItemController@moveItemsToNewOrder')->name('stock-order-items.move-to-new-order');
    Route::resource('stock-order-items', 'StockOrderItemController')->except(['index', 'show']);

    // Purchase order controller
    Route::resource('purchase-orders', 'PurchaseOrderController')->only(['index']);
});
