<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('roles', RoleController::class)->names([
        'index' => 'web.roles.index',
        'create' => 'web.roles.create',
        'store' => 'web.roles.store',
        'show' => 'web.roles.show',
        'edit' => 'web.roles.edit',
        'update' => 'web.roles.update',
        'destroy' => 'web.roles.destroy',
    ]);
    Route::resource('users', UserController::class)->names([
        'index' => 'web.users.index',
        'create' => 'web.users.create',
        'store' => 'web.users.store',
        'show' => 'web.users.show',
        'edit' => 'web.users.edit',
        'update' => 'web.users.update',
        'destroy' => 'web.users.destroy',
    ]);
});
