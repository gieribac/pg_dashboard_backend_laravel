<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorizationController;

Route::prefix('authorized')->group(function () {
    Route::get('/', [AuthorizationController::class, 'index']); //Consulta general
    Route::post('/', [AuthorizationController::class, 'create']); // Crear un registro
    Route::delete('/{id}', [AuthorizationController::class, 'delete']); // Eliminar un registro
});