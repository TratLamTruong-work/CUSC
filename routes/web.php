<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tasks');

// Chỉ dành cho khách (chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Chỉ dành cho người đã đăng nhập
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus'])
        ->whereNumber('id')
        ->name('tasks.status');

    Route::resource('tasks', TaskController::class)
        ->parameters(['tasks' => 'id'])
        ->whereNumber('id');
});
