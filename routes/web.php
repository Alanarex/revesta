<?php

use App\Http\Controllers\ConditionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(IsAdmin::class)->group(function () {
        route::group(['prefix' => 'conditions', 'as' => 'conditions.'], function () {
            Route::get('/', [ConditionsController::class, 'index'])->name('index');
            Route::post('/update', [ConditionsController::class, 'update'])->name('update');
        });
    });
});



// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__ . '/auth.php';
