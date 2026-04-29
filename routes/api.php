<?php

use App\Http\Controllers\api\AuthenticationController;
use App\Http\Controllers\API\PSGC_new\Q12026\CityController;
use App\Http\Controllers\API\PSGC_new\Q12026\ClassificationController;
use App\Http\Controllers\API\PSGC_new\Q12026\MunicipalityController;
use App\Http\Controllers\API\PSGC_new\Q12026\ProvinceController;
use App\Http\Controllers\API\PSGC_new\Q12026\PSGCController;
use App\Http\Controllers\API\PSGC_new\Q12026\RegionController;
use App\Http\Controllers\API\PSGC_new\Q12026Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --------------- Register and Login ----------------//
Route::post("register", [AuthenticationController::class, "register"])->name("api.register",);
Route::post("login", [AuthenticationController::class, "login"])->name("api.login");

// --------------- PSGC Data Routes ----------------//
// Main PSGC endpoints
Route::get("psgc", [PSGCController::class, "index"])->name("api.psgc");
Route::get("psgc/search", [PSGCController::class, "search"])->name("api.psgc.search",);
Route::get("psgc/{psgc_code}", [PSGCController::class, "show"])->name("api.psgc.show",);

// Classification endpoints
Route::get("geographic-levels", [ClassificationController::class,"geographic_levels",])->name("api.geographic_levels");
Route::get("city-classifications", [ClassificationController::class,"city_classifications",])->name("api.city_classifications");
Route::get("income-classifications", [ClassificationController::class,"income_classifications",])->name("api.income_classifications");
Route::get("urban-rural-classifications", [ClassificationController::class,"urban_rural_classifications",])->name("api.urban_rural_classifications");

// Region endpoints
Route::get("regions", [RegionController::class, "regions"])->name("api.regions",);
Route::get("regions/{psgc_code}", [RegionController::class, "region"])->name("api.region");
Route::get("regions/{psgc_code}/provinces", [RegionController::class,"region_provinces",])->name("api.region.provinces");
Route::get("regions/{psgc_code}/cities", [RegionController::class,"region_cities",])->name("api.region.cities");
Route::get("regions/{psgc_code}/municipalities", [RegionController::class,"region_municipalities",])->name("api.region.municipalities");
Route::get("regions/{psgc_code}/sub-municipalities", [RegionController::class,"region_submunicipalities",])->name("api.region.sub-municipalities");
Route::get("regions/{psgc_code}/barangays", [RegionController::class,"region_barangays",])->name("api.region.barangays");

// Province endpoints
Route::get("provinces", [ProvinceController::class, "provinces"])->name("api.provinces");
Route::get("provinces/{psgc_code}", [ProvinceController::class,"province",])->name("api.province");
Route::get("provinces/{psgc_code}/cities", [ProvinceController::class,"province_cities",])->name("api.province.cities");
Route::get("provinces/{psgc_code}/barangays", [ProvinceController::class,"province_barangays",])->name("api.province.barangays");

// City endpoints
Route::get("cities", [CityController::class, "cities"])->name("api.cities");
Route::get("cities/{psgc_code}", [CityController::class, "city"])->name("api.city");
Route::get("cities/{psgc_code}/barangays", [CityController::class,"municity_barangays",])->name("api.city.barangays");
Route::get("cities/classification/highly-urbanized", [CityController::class,"highly_urbanized_cities",])->name("api.cities.huc");
Route::get("cities/classification/component", [CityController::class,"component_cities"])->name("api.cities.component");
Route::get("cities/classification/independent-component", [CityController::class,"independent_component_cities",])->name("api.cities.independent-component");

// Municipality endpoints
Route::get("municipalities", [MunicipalityController::class, "municipalities"])->name("api.municipalities",);
Route::get("municipalities/{psgc_code}", [MunicipalityController::class,"municipality",])->name("api.municipality");

// Sub-Municipality endpoints
Route::get("sub-municipalities", [Q12026Controller::class,"sub_municipalities",])->name("api.sub_municipalities");
Route::get("sub-municipalities/{psgc_code}", [Q12026Controller::class,"sub_municipality",])->name("api.sub_municipality");

// Barangay endpoints
Route::get("barangays", [Q12026Controller::class, "barangays"])->name("api.barangays",);
Route::get("barangays/{psgc_code}", [Q12026Controller::class,"barangay",])->name("api.barangay");

// Legacy endpoints for backward compatibility
Route::get("geographic_level", [Q12026Controller::class,"geographic_level",])->name("api.geographic_level");
Route::get("city_classification", [Q12026Controller::class,"city_classification",])->name("api.city_classification");
Route::get("income_classification", [Q12026Controller::class,"income_classification",])->name("api.income_classification");

// Protected routes
Route::middleware("auth:sanctum")->group(function () {
    Route::get("user", [AuthenticationController::class, "userInfo"])->name("api.get-user",);
    Route::post("logout", [AuthenticationController::class, "logOut"])->name("api.logout",);
});
