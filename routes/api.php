<?php

require __DIR__ . '/admin.php';
require __DIR__ . '/admin-auth.php';

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MapController;

Route::prefix('maps')->group(function () {
    Route::get('/',[MapController::class,'index']);
    Route::get('/{id}',[MapController::class,'show']);
    Route::post('/',[MapController::class,'store']);
    Route::put('/{id}',[MapController::class,'update']);
    Route::patch('/{id}',[MapController::class,'updatePartial']);
    Route::delete('/{id}',[MapController::class,'destroy']);
});