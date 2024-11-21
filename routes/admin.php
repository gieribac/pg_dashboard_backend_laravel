<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index']);//consultar los registros
    Route::post('/', [AdminController::class, 'store']); //crear registro 
    Route::get('/{id}', [AdminController::class, 'show']);
    Route::put('/{id}', [AdminController::class, 'update']);
    Route::patch('/{id}', [AdminController::class, 'updatePartial']);
    Route::delete('/{id}', [AdminController::class, 'destroy']);
});
