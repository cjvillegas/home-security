<?php
Route::group(['prefix' => 'public', 'as' => 'public.', 'namespace' => 'PublicAccessible'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/dashboard/orders-data', 'DashboardController@ordersData')->name('dashboard.orders-data');
});
