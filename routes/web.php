<?php

use App\Http\Controllers\ConditionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::controller(ProfileController::class)
        ->prefix('profile')
        ->name('profile.')
        ->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::patch('/', 'update')->name('update');
            Route::put('/password', 'updatePassword')->name('password.update');
            Route::delete('/', 'destroy')->name('destroy');
        });

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
require __DIR__ . '/blogs.php';

// Public user profile (view another user's profile)
// Use /profile/{userId} so profile routes stay under the 'profile' prefix and
// views that link to profiles can use route('profile.show', ['userId' => ...])
Route::get('/profile/{userId}', [ProfileController::class, 'show'])->name('profile.show');
