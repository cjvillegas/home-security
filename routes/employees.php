<?php

Route::group(['prefix' => 'employee', 'as' => 'employee.', 'namespace' => 'Employee'], function () {
    Route::get('/', 'LoginController@index');
    Route::get('/login', 'LoginController@index');
    Route::post('/login', 'LoginController@login');
    Route::get('/get-employee-by-barcode', 'EmployeeController@getEmployeeByBarcode')->name('employee.get-employee-by-barcode');

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/index', 'EmployeeController@index');

        //Overtime
        Route::get('/overtime-booking', 'EmployeeOvertimeController@index');
    });
});
