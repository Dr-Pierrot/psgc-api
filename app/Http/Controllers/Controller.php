<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "PSGC API",
    description: "API for Philippine Standard Geographic Code (PSGC) data. Based on the latest PSGC data available as of 2026.",
    contact: new OA\Contact(
        name: "Dr-Pierrot",
        url: "https://github.com/Dr-Pierrot",
        email: "capulongako16gmail.com",
    ),
)]


#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "apiKey",
    description: "Enter token in format (Bearer <token>)",
    name: "Authorization",
    in: "header"
)]

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
