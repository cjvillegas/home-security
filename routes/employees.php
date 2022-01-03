<?php

Route::group(['prefix' => 'employee', 'as' => 'employee.', 'namespace' => 'Employee'], function () {
    Route::get('/', 'LoginController@index');
    Route::get('/login', 'LoginController@index');
    Route::post('/login', 'LoginController@login');
    Route::get('/get-employee-by-barcode', 'EmployeeController@getEmployeeByBarcode')->name('employee.get-employee-by-barcode');

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/index', 'EmployeeController@index');
    });

     //Overtime
});

Route::group(['namespace' => 'Employee'], function() {
    Route::post('/barcode', 'EmployeeOvertimeController@getEmployee');
    Route::get('/overtime-booking', 'EmployeeOvertimeController@index');
    Route::get('/overtime-bookings/available', 'EmployeeOvertimeController@getAvailableSlots');
    Route::post('/overtime-bookings/store-selected-slots', 'EmployeeOvertimeController@store');
});
