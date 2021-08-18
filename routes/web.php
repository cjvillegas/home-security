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
    Route::post('permissions/list', 'PermissionsController@fetchPermissions');
    Route::resource('permissions', 'PermissionsController')->only(['index', 'store', 'update', 'destroy']);

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::post('users/get-list', 'UsersController@getList')->name('users.get-list');
    Route::patch('users/{user}/status-change', 'UsersController@changeStatus')->name('users.status-change');
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // User Alerts
    // Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    // Route::get('user-alerts/read', 'UserAlertsController@read');
    // Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

     //Scanners
    Route::get('scanners/get-scanners-by-field', 'ScannersController@getScannersByField')->name('scanners.get-scanners-by-field');
    Route::get('scanners/search-scanners-by-field', 'ScannersController@searchScannersByField')->name('orders.search-scanners-by-field');
    // Route::resource('scanners', 'ScannersController');

    // Employees
    Route::delete('employees/destroy', 'EmployeesController@massDestroy')->name('employees.massDestroy');
    Route::post('employees/get-list', 'EmployeesController@getList')->name('employees.get-list');
    Route::patch('employees/{employee}/status-change', 'EmployeesController@changeStatus')->name('employees.status-change');
    Route::post('employees/parse-csv-import', 'EmployeesController@parseCsvImport')->name('employees.parseCsvImport');
    Route::post('employees/process-csv-import', 'EmployeesController@processCsvImport')->name('employees.processCsvImport');
    Route::get('employees/{employee}/print-barcode', 'EmployeesController@printBarcode')->name('employees.print-barcode');

    Route::resource('employees', 'EmployeesController')->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::post('employees/search', 'EmployeesController@searchEmployee');

    // Processes
    Route::delete('processes/destroy', 'ProcessesController@massDestroy')->name('processes.massDestroy');
    Route::post('processes/parse-csv-import', 'ProcessesController@parseCsvImport')->name('processes.parseCsvImport');
    Route::post('processes/process-csv-import', 'ProcessesController@processCsvImport')->name('processes.processCsvImport');
    Route::post('processes/get-all', 'ProcessesController@getAllProcesses')->name('processes.get-all');
    Route::resource('processes', 'ProcessesController');

    // Orders
    Route::delete('orders/destroy', 'OrdersController@massDestroy')->name('orders.massDestroy');
    Route::post('orders/parse-csv-import', 'OrdersController@parseCsvImport')->name('orders.parseCsvImport');
    Route::post('orders/process-csv-import', 'OrdersController@processCsvImport')->name('orders.processCsvImport');
    Route::get('orders/vieworderno/{id}', [OrdersController::class, 'vieworderno'])->name('orders.vieworderno');
    Route::get('orders/fetch', 'OrdersController@fetch')->name('orders.fetch');
    Route::get('orders/search-orders-by-field', 'OrdersController@searchOrdersByField')->name('orders.search-orders-by-field');
    Route::get('/orders/{to_search}/order-list', 'OrdersController@showOrderList')->name('orders.order-list');
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
    Route::get('shifts/list', 'ShiftsController@fetchShifts');
    Route::resource('shifts', 'ShiftsController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Orderhistory
    Route::delete('orderhistories/destroy', 'OrderhistoryController@massDestroy')->name('orderhistories.massDestroy');
    Route::resource('orderhistories', 'OrderhistoryController');

    // route collection for reports
    require_once base_path('routes/web/reports.php');

    // route collection for exports
    require_once base_path('routes/web/exports.php');

    Route::get('/process-categories', 'Settings\ProcessCategoryController@index')->name('process-categories.index')->middleware('can:process_categories_access');
    // Process Category
    Route::prefix('settings')->as('settings.')->group(function () {
        Route::post('/process-category/get-list', 'Settings\ProcessCategoryController@getList')->name('process-category.get-list');
        Route::apiResource('process-category', 'Settings\ProcessCategoryController')->only(['store', 'show', 'update', 'destroy']);
    });

    // Process Sequence
    Route::prefix('/process-sequence')->namespace('Sequence')->as('process-sequence.')->group(function () {
        Route::post('get-list', 'ProcessSequenceController@getList')->name('get-list');
        Route::resource('', 'ProcessSequenceController')->parameters([
           '' => 'process-sequence'
        ]);

        Route::post('{id}/add-new-step', 'ProcessSequenceLinkController@store')->name('process-sequence-link.store');
        Route::put('{process_sequence}/move-step-order/{process_sequence_link}', 'ProcessSequenceLinkController@moveStepOrder')->name('process-sequence-link.move-step-order');
        Route::get('{process_sequence}/steps', 'ProcessSequenceLinkController@index')->name('process-sequence-link.index');
        Route::delete('{process_sequence}/delete-step/{process_sequence_link}', 'ProcessSequenceLinkController@destroy')->name('process-sequence-link.index');
    });

    //Machine
    Route::prefix('machines')->as('machines')->group(function () {
        Route::get('/', 'MachineController@index');
        Route::post('machines-list', 'MachineController@fetchMachines');

        Route::post('store', 'MachineController@store')->name('machine.store');
        Route::patch('{machine}/update', 'MachineController@update')->name('machine.update');
        Route::delete('{machine}/destroy', 'MachineController@destroy');
    });

    //Machine Counter
    Route::prefix('machine-counters')->as('machine-counters')->group(function () {
        Route::get('/', 'MachineCounterController@index')->name('machine-counters.index');

        //api
        Route::post('/list', 'MachineCounterController@fetchMachineCounters');
        Route::post('store', 'MachineCounterController@store');
        Route::patch('{machineCounter}/update', 'MachineCounterController@update');
        Route::delete('{machineCounter}/destroy', 'MachineCounterController@destroy');
    });

    require_once base_path('routes/in-house.php');

    // notifications
    Route::get('/notifications/get-list', 'NotificationController@getList')->name('notifications.get-list');
    Route::resource('notifications', 'NotificationController')->only(['index', 'show', 'destroy']);
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

require_once base_path('routes/employees.php');

