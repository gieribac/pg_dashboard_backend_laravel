<?php
//api.php
require __DIR__ . '/admin.php';
require __DIR__ . '/admin-auth.php';
require __DIR__ . '/authorized.php';
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MapController;

Route::prefix('maps')->group(function () {
    Route::get('/',[MapController::class,'index']);
    Route::get('/{id}',[MapController::class,'show'])->middleware('jwt.auth');
    Route::post('/',[MapController::class,'store'])->middleware('jwt.auth');
    Route::put('/{id}',[MapController::class,'update'])->middleware('jwt.auth');
    Route::patch('/{id}',[MapController::class,'updatePartial'])->middleware('jwt.auth');
    Route::delete('/{id}',[MapController::class,'destroy'])->middleware('jwt.auth');
});