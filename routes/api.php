<?php

use App\Http\Controllers\api\AuthenticationController;
use App\Http\Controllers\API\PSGC\Q12026Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// --------------- Register and Login ----------------//
Route::post('register', [AuthenticationController::class, 'register'])->name('api.register');
Route::post('login', [AuthenticationController::class, 'login'])->name('api.login');
Route::get('psgc', [Q12026Controller::class, 'index'])->name('api.psgc');

// ------------------ Get Data ----------------------//
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthenticationController::class, 'userInfo'])->name('api.get-user');
    Route::post('logout', [AuthenticationController::class, 'logOut'])->name('api.logout');
});
