<?php

use App\Http\Controllers\api\AuthenticationController;
use App\Http\Controllers\API\PSGC_new\Q12026Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --------------- Register and Login ----------------//
Route::post("register", [AuthenticationController::class, "register"])->name(
    "api.register",
);
Route::post("login", [AuthenticationController::class, "login"])->name(
    "api.login",
);

// --------------- PSGC Data Routes ----------------//
// Main PSGC endpoints
Route::get("psgc", [Q12026Controller::class, "index"])->name("api.psgc");
Route::get("psgc/search", [Q12026Controller::class, "search"])->name(
    "api.psgc.search",
);
Route::get("psgc/{psgc_code}", [Q12026Controller::class, "show"])->name(
    "api.psgc.show",
);

// Classification endpoints
Route::get("geographic-levels", [
    Q12026Controller::class,
    "geographic_levels",
])->name("api.geographic_levels");
Route::get("city-classifications", [
    Q12026Controller::class,
    "city_classifications",
])->name("api.city_classifications");
Route::get("income-classifications", [
    Q12026Controller::class,
    "income_classifications",
])->name("api.income_classifications");
Route::get("urban-rural-classifications", [
    Q12026Controller::class,
    "urban_rural_classifications",
])->name("api.urban_rural_classifications");

// Region endpoints
Route::get("regions", [Q12026Controller::class, "regions"])->name(
    "api.regions",
);
Route::get("regions/{psgc_code}", [Q12026Controller::class, "region"])->name(
    "api.region",
);
Route::get("regions/{psgc_code}/provinces", [
    Q12026Controller::class,
    "region_provinces",
])->name("api.region.provinces");
Route::get("regions/{psgc_code}/cities", [
    Q12026Controller::class,
    "region_cities",
])->name("api.region.cities");
Route::get("regions/{psgc_code}/municipalities", [
    Q12026Controller::class,
    "region_municipalities",
])->name("api.region.municipalities");
Route::get("regions/{psgc_code}/sub-municipalities", [
    Q12026Controller::class,
    "region_submunicipalities",
])->name("api.region.sub-municipalities");
Route::get("regions/{psgc_code}/barangays", [
    Q12026Controller::class,
    "region_barangays",
])->name("api.region.barangays");

// Province endpoints
Route::get("provinces", [Q12026Controller::class, "provinces"])->name(
    "api.provinces",
);
Route::get("provinces/{psgc_code}", [
    Q12026Controller::class,
    "province",
])->name("api.province");
Route::get("provinces/{psgc_code}/cities", [
    Q12026Controller::class,
    "province_cities",
])->name("api.province.cities");
Route::get("provinces/{psgc_code}/barangays", [
    Q12026Controller::class,
    "province_barangays",
])->name("api.province.barangays");

// City endpoints
Route::get("cities", [Q12026Controller::class, "cities"])->name("api.cities");
Route::get("cities/{psgc_code}", [Q12026Controller::class, "city"])->name(
    "api.city",
);
Route::get("cities/{psgc_code}/barangays", [
    Q12026Controller::class,
    "municity_barangays",
])->name("api.city.barangays");
Route::get("cities/classification/highly-urbanized", [
    Q12026Controller::class,
    "highly_urbanized_cities",
])->name("api.cities.huc");
Route::get("cities/classification/component", [
    Q12026Controller::class,
    "component_cities",
])->name("api.cities.component");
Route::get("cities/classification/independent-component", [
    Q12026Controller::class,
    "independent_component_cities",
])->name("api.cities.independent-component");

// Municipality endpoints
Route::get("municipalities", [Q12026Controller::class, "municipalities"])->name(
    "api.municipalities",
);
Route::get("municipalities/{psgc_code}", [
    Q12026Controller::class,
    "municipality",
])->name("api.municipality");

// Sub-Municipality endpoints
Route::get("sub-municipalities", [
    Q12026Controller::class,
    "sub_municipalities",
])->name("api.sub_municipalities");
Route::get("sub-municipalities/{psgc_code}", [
    Q12026Controller::class,
    "sub_municipality",
])->name("api.sub_municipality");

// Barangay endpoints
Route::get("barangays", [Q12026Controller::class, "barangays"])->name(
    "api.barangays",
);
Route::get("barangays/{psgc_code}", [
    Q12026Controller::class,
    "barangay",
])->name("api.barangay");

// Legacy endpoints for backward compatibility
Route::get("geographic_level", [
    Q12026Controller::class,
    "geographic_level",
])->name("api.geographic_level");
Route::get("city_classification", [
    Q12026Controller::class,
    "city_classification",
])->name("api.city_classification");
Route::get("income_classification", [
    Q12026Controller::class,
    "income_classification",
])->name("api.income_classification");

// Protected routes
Route::middleware("auth:sanctum")->group(function () {
    Route::get("user", [AuthenticationController::class, "userInfo"])->name(
        "api.get-user",
    );
    Route::post("logout", [AuthenticationController::class, "logOut"])->name(
        "api.logout",
    );
});
