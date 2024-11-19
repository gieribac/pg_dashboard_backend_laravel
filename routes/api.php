<?php

require __DIR__ . '/admin.php';

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\mapController;

Route::get('/maps',[mapController::class,'index']);
Route::get('/maps/{id}',[mapController::class,'show']);


Route::post('/maps',[mapController::class,'store']);


Route::put('/maps/{id}',[mapController::class,'update']);

Route::patch('/maps/{id}',[mapController::class,'updatePartial']);

Route::delete('/maps/{id}',[mapController::class,'destroy']);