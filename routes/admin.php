<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\adminController;

Route::prefix('admin')->group(function () {
    Route::get('/', [adminController::class, 'index']);//consultar los registros
    Route::post('/', [adminController::class, 'store']); //crear registro 
    Route::get('/{id}', [adminController::class, 'show']);
    Route::put('/{id}', [adminController::class, 'update']);
    Route::delete('/{id}', [adminController::class, 'destroy']);
});
