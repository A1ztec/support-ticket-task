<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');

});

    Route::prefix('admin')->name('admin.')->group(function()
    {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::get('login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('login', [AuthController::class, 'login'])->name('login.post');
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        });

        Route::middleware(['auth' , 'admin'])->group(function () {

        });
    });
