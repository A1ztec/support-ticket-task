<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {

        Route::controller(AuthController::class)->group(function () {
            Route::post('/login', 'login')->name('auth.login')->middleware('throttle:10,1');
            Route::post('/register', 'register')->name('auth.register')->middleware('throttle:5,1');
            Route::post('/verification/resend', 'resendVerificationCode')->name('auth.verification.resend')->middleware('throttle:5,1');
            Route::post('/verification/verify', 'verifyEmail')->name('auth.verification.verify')->middleware('throttle:5,1');

            Route::middleware('auth:sanctum')->group(function () {
                Route::post('/logout', 'logout')->name('auth.logout');
            });
        });
    });


    Route::middleware(['auth:sanctum', 'verified'])->group(function () {

        Route::prefix('tickets')->group(function () {
            Route::get('/', [TicketController::class, 'listTickets']);
            Route::post('/', [TicketController::class, 'createTicket']);
            Route::get('/{ticket}', [TicketController::class, 'showTicket']);
            Route::post('/reply', [TicketController::class, 'reply']);
            Route::get('/{ticket}/download-attachment', [TicketController::class, 'downloadAttachment'])
                ->name('tickets.downloadAttachment');
        });
    });
});

