<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::post('permissions/list', 'PermissionsController@fetchPermissions');
    Route::resource('permissions', 'PermissionsController')->only(['index', 'store', 'update', 'destroy']);

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::post('roles/list', 'RolesController@fetchRoles');
    Route::get('roles/permissions', 'RolesController@fetchAllPermissions');
    Route::resource('roles', 'RolesController');

    // Users
    Route::post('users/get-list', 'UsersController@getList')->name('users.get-list');
    Route::get('users/get-clean-users', 'UsersController@getCleanUsers')->name('users.get-clean-users');
    Route::patch('users/{user}/status-change', 'UsersController@changeStatus')->name('users.status-change');
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::get('users/get-auth-user', 'UsersController@getAuthUser')->name('users.get-auth-user');
    Route::get('users/check-privacy', 'UsersController@checkPrivacy')->name('users.check-privacy');
    Route::resource('users', 'UsersController');

    Route::get('monitorings/list', 'MonitoringController@list')->name('monitorings.list');
    Route::resource('monitorings', 'MonitoringController');

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
