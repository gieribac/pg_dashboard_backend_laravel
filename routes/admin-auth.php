<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('jwt.auth');
    Route::get('me', [AdminAuthController::class, 'me'])->middleware('jwt.auth');
});
