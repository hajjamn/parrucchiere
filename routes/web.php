<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\SwitchUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceLogController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::prefix('dreamscenter')->group(function () {

    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('admin.service-logs.index');
        }

        return view('auth.login');
    });

    Route::middleware(['auth', 'verified'])
        ->name('admin.')
        ->prefix('admin')
        ->group(function () {

            Route::get('/', function () {
                return view('admin.dashboard');
            })->name('dashboard');

            Route::resource('clients', ClientController::class);
            Route::resource('services', ServiceController::class);
            Route::resource('service-logs', ServiceLogController::class);
            Route::resource('users', UserController::class);

            Route::controller(SwitchUserController::class)->prefix('switch-user')->name('switch-user.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'switch')->name('switch');
            });
        });

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    require __DIR__ . '/auth.php';
});
