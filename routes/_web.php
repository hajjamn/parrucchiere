<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\SwitchUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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

        //Client Routes
        Route::resource('clients', ClientController::class);
        //Service Routes
        Route::resource('services', ServiceController::class);
        //ServiceLogs Routes
        Route::resource('service-logs', ServiceLogController::class);
        //User Routes
        Route::resource('users', UserController::class);

        // Switch User Routes
        Route::controller(SwitchUserController::class)->prefix('switch-user')->name('switch-user.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'switch')->name('switch');
        });


    });

Route::middleware('auth')->group(function () {

    //Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Client routes
    /* Route::resource('clients', ClientController::class); */

});

require __DIR__ . '/auth.php';
