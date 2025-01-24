<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;

Route::prefix('adminlog')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('jwt.auth');
    Route::get('/me', [AdminAuthController::class, 'me'])->middleware('jwt.auth');
    Route::patch('/updatepassword', [AdminAuthController::class, 'updatePassword'])->middleware('jwt.auth');;
});
// administradores
// 1
// gieribac@gmail.com
// 123qwertyAz
// 2
// gioiban3z@gmail.com
// 564dssdfAa
// 3
// {
//     "name": "judaa",
//     "no_doc": "102184771",
//     "email": "yeicoafqw@gmail.com",
//     "username": "juraameloqwq",
//     "password": "drrb121e13",
//     "main": true
//   }
