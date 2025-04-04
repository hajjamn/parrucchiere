<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
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
    return view('welcome');
});

Route::middleware(['auth', 'verified'])
    ->name('admin.')
    ->prefix('admin')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        //Client Routes
        Route::resource('clients', ClientController::class)->names([
            'index' => 'client.index',
            'create' => 'client.create',
            'store' => 'client.store',
            'show' => 'client.show',
            'edit' => 'client.edit',
            'update' => 'client.update',
            'destroy' => 'client.destroy'
        ]);

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
