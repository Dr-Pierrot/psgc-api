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

#[OA\Tag(name: "Authentication", description: "The authentication endpoint.", x:["order" => 1])]
#[OA\Tag(name: "PSGC", description: "The PSGC endpoint.", x:["order" => 2])]
#[OA\Tag(name: "Classification", description: "The classification endpoint.", x:["order" => 3])]
#[OA\Tag(name: "Regions", description: "The regions endpoint.", x:["order" => 4])]
#[OA\Tag(name: "Provinces", description: "The provinces endpoint.", x:["order" => 5])]
#[OA\Tag(name: "Cities", description: "The cities endpoint.", x:["order" => 6])]
#[OA\Tag(name: "Municipalities", description: "The municipalities endpoint.", x:["order" => 7])]
#[OA\Tag(name: "Sub-Municipalities", description: "The sub-municipalities endpoint.", x:["order" => 8])]
#[OA\Tag(name: "Barangays", description: "The barangays endpoint.", x:["order" => 9])]


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
