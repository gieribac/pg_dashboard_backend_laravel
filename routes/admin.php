<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->middleware('jwt.auth');
    Route::post('/', [AdminController::class, 'store']); //crear registro 
    Route::get('/{id}', [AdminController::class, 'show'])->middleware('jwt.auth');
    Route::patch('/{id}', [AdminController::class, 'updatePartial'])->middleware('jwt.auth');
});