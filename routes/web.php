<?php

use App\Http\Controllers\Admin\OrdersController;

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Scanners
    Route::delete('scanners/destroy', 'ScannersController@massDestroy')->name('scanners.massDestroy');
    Route::post('scanners/parse-csv-import', 'ScannersController@parseCsvImport')->name('scanners.parseCsvImport');
    Route::post('scanners/process-csv-import', 'ScannersController@processCsvImport')->name('scanners.processCsvImport');
    Route::match(['post', 'get'], 'scanners/fetch-scanners', 'ScannersController@fetchScanners')->name('scanners.fetch-scanners');
    Route::resource('scanners', 'ScannersController');

    // Employees
    Route::delete('employees/destroy', 'EmployeesController@massDestroy')->name('employees.massDestroy');
    Route::post('employees/parse-csv-import', 'EmployeesController@parseCsvImport')->name('employees.parseCsvImport');
    Route::post('employees/process-csv-import', 'EmployeesController@processCsvImport')->name('employees.processCsvImport');
    Route::get('employees/{employee}/print-barcode', 'EmployeesController@printBarcode')->name('employees.print-barcode');
    Route::resource('employees', 'EmployeesController');

    // Processes
    Route::delete('processes/destroy', 'ProcessesController@massDestroy')->name('processes.massDestroy');
    Route::post('processes/parse-csv-import', 'ProcessesController@parseCsvImport')->name('processes.parseCsvImport');
    Route::post('processes/process-csv-import', 'ProcessesController@processCsvImport')->name('processes.processCsvImport');
    Route::resource('processes', 'ProcessesController');

    // Orders
    Route::delete('orders/destroy', 'OrdersController@massDestroy')->name('orders.massDestroy');
    Route::post('orders/parse-csv-import', 'OrdersController@parseCsvImport')->name('orders.parseCsvImport');
    Route::post('orders/process-csv-import', 'OrdersController@processCsvImport')->name('orders.processCsvImport');
    Route::get('orders/vieworderno/{id}', [OrdersController::class, 'vieworderno'])->name('orders.vieworderno');
    Route::resource('orders', 'OrdersController');

    // Teams
    Route::delete('teams/destroy', 'TeamsController@massDestroy')->name('teams.massDestroy');
    Route::post('teams/parse-csv-import', 'TeamsController@parseCsvImport')->name('teams.parseCsvImport');
    Route::post('teams/process-csv-import', 'TeamsController@processCsvImport')->name('teams.processCsvImport');
    Route::resource('teams', 'TeamsController');

    // Shifts
    Route::delete('shifts/destroy', 'ShiftsController@massDestroy')->name('shifts.massDestroy');
    Route::post('shifts/parse-csv-import', 'ShiftsController@parseCsvImport')->name('shifts.parseCsvImport');
    Route::post('shifts/process-csv-import', 'ShiftsController@processCsvImport')->name('shifts.processCsvImport');
    Route::resource('shifts', 'ShiftsController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Orderhistory
    Route::delete('orderhistories/destroy', 'OrderhistoryController@massDestroy')->name('orderhistories.massDestroy');
    Route::resource('orderhistories', 'OrderhistoryController');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
