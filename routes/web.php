<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\DashboardController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\Api\TicketController as ApiTicketController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.post');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('index');
        });
        Route::controller(TicketController::class)->name('tickets.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{ticket}', 'show')->name('show');
            Route::post('/{ticket}/reply', 'reply')->name('reply');
            Route::patch('/{ticket}/status', 'updateStatus')->name('update-status');
            Route::patch('/{ticket}/close', 'close')->name('close');
            Route::get('/{ticket}/download-attachment', [ApiTicketController::class, 'downloadAttachment'])->name('downloadAttachment');
        });
    });
});

Route::get('/sse-test', function () {
    return view('sse');
});

Route::get('/sse', function () {
    return response()->stream(function () {
        while (true) {
            echo "data: " . json_encode(['time' => now()->toDateTimeString()]) . "\n\n";
            ob_flush();
            flush();
            sleep(2);
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
});
