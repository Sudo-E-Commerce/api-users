<?php
App::booted(function() {
	$namespace = 'Sudo\ApiUser\Http\Controllers';

	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin'])->group(function() {
		// Quản lý API token
		Route::resource('api_users', 'ApiUserController');
	});
});