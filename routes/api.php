<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProjectController;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;


Route::middleware([RedirectIfAuthenticated::class])->group(function (){
    Route::post('login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('register', [AuthenticationController::class, 'register'])->name('register');
});


Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('projects')->name('project.')->group(function (){
        Route::get('', [ProjectController::class, 'index'])->name('index');
        Route::post('', [ProjectController::class, 'store'])->name('store');
        Route::get('{project}', [ProjectController::class, 'show'])->name('show');
        Route::delete('{project}', [ProjectController::class, 'destroy'])->name('destroy');
        Route::patch('{project}', [ProjectController::class, 'restore'])->name('restore');
    });

    Route::prefix('employees')->name('employee.')->group(function (){
        Route::get('', [EmployeeController::class, 'index'])->name('index');
        Route::post('', [EmployeeController::class, 'store'])->name('store');
        Route::get('{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::delete('{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::patch('{employee}', [EmployeeController::class, 'restore'])->name('restore');
    });

});
