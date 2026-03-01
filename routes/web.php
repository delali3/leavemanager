<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('home');
})->name('home');

// Redirect direct GET /logout visits to login (logout requires POST for CSRF safety)
Route::get('/logout', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Leave Requests — all authenticated users
    |--------------------------------------------------------------------------
    */
    Route::resource('leave-requests', LeaveRequestController::class)
        ->except(['edit', 'update']);

    // Approval actions (managers, HR, admin)
    Route::post('leave-requests/{leave_request}/approve', [LeaveRequestController::class, 'approve'])
        ->name('leave-requests.approve');

    Route::post('leave-requests/{leave_request}/reject', [LeaveRequestController::class, 'reject'])
        ->name('leave-requests.reject');

    /*
    |--------------------------------------------------------------------------
    | Leave Types — Admin & HR only
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin|hr')->group(function () {
        Route::resource('leave-types', LeaveTypeController::class);
        Route::post('leave-types/{id}/restore', [LeaveTypeController::class, 'restore'])
            ->name('leave-types.restore');
    });

    /*
    |--------------------------------------------------------------------------
    | Users — Admin & HR only
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin|hr')->group(function () {
        Route::resource('users', UserController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Reports — Admin, HR, Manager
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin|hr|manager')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
});

require __DIR__ . '/auth.php';
