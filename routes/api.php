<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

    // Scanners
    Route::apiResource('scanners', 'ScannersApiController');

    // Employees
    Route::apiResource('employees', 'EmployeesApiController');

    // Processes
    Route::apiResource('processes', 'ProcessesApiController');

    // Orders
    Route::apiResource('orders', 'OrdersApiController');

    // Teams
    Route::apiResource('teams', 'TeamsApiController');

    // Shifts
    Route::apiResource('shifts', 'ShiftsApiController');

    // Orderhistory
    Route::apiResource('orderhistories', 'OrderhistoryApiController');
});
