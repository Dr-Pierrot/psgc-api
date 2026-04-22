<?php

use App\Http\Controllers\api\AuthenticationController;
use App\Http\Controllers\API\PSGC\Q12026Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// --------------- Register and Login ----------------//
Route::post('register', [AuthenticationController::class, 'register'])->name('api.register');
Route::post('login', [AuthenticationController::class, 'login'])->name('api.login');
Route::get('psgc', [Q12026Controller::class, 'index'])->name('api.psgc');
Route::get('geographic_level', [Q12026Controller::class, 'geographic_level'])->name('api.geographic_level');
Route::get('city_classification', [Q12026Controller::class, 'city_classification'])->name('api.city_classification');
Route::get('income_classification', [Q12026Controller::class, 'income_classification'])->name('api.income_classification');

Route::get('regions', [Q12026Controller::class, 'regions'])->name('api.regions');
Route::get('region/{psgc_code}', [Q12026Controller::class, 'region'])->name('api.region');
Route::get('region/{psgc_code}/provinces', [Q12026Controller::class, 'region_provinces'])->name('api.region.provinces');
Route::get('region/{psgc_code}/cities', [Q12026Controller::class, 'region_cities'])->name('api.region.cities');
Route::get('region/{psgc_code}/municipalities', [Q12026Controller::class, 'region_municipalities'])->name('api.region.municipalities');
Route::get('region/{psgc_code}/sub-municipalities', [Q12026Controller::class, 'region_submunicipalities'])->name('api.region.sub-municipalities');
Route::get('region/{psgc_code}/barangays', [Q12026Controller::class, 'region_barangays'])->name('api.region.barangays');




// ------------------ Get Data ----------------------//
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthenticationController::class, 'userInfo'])->name('api.get-user');


    Route::post('logout', [AuthenticationController::class, 'logOut'])->name('api.logout');
});
