<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->middleware('jwt.auth');;//consultar los registros
    Route::post('/', [AdminController::class, 'store'])->middleware('jwt.auth');; //crear registro 
    Route::get('/{id}', [AdminController::class, 'show'])->middleware('jwt.auth');;
    Route::put('/{id}', [AdminController::class, 'update'])->middleware('jwt.auth');;
    Route::patch('/{id}', [AdminController::class, 'updatePartial'])->middleware('jwt.auth');;
    Route::delete('/{id}', [AdminController::class, 'destroy'])->middleware('jwt.auth');;
});
