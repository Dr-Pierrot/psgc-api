<?php

namespace App\Http\Controllers\API\PSGC_new\Q12026;

use App\Http\Controllers\Controller;
use App\Models\PSGC_new\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class CityController extends Controller
{
    #[
        OA\Get(
            path: "/api/cities/{psgc_code}/barangays",
            summary: "Get barangays in a city/municipality",
            description: "Retrieve all barangays in a specific city or municipality using PSGC code",
            tags: ["Cities"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "City/Municipality PSGC code",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "string"),
                ),
                new OA\Parameter(
                    name: "page",
                    description: "Page number",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 1),
                ),
                new OA\Parameter(
                    name: "per_page",
                    description: "Records per page",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 15),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Barangays retrieved successfully",
                ),
                new OA\Response(
                    response: 404,
                    description: "City/Municipality not found",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function municity_barangays(string $psgcCode, Request $request)
    {
        try {
            $municity = Q12026::where(function ($query) use ($psgcCode) {
                $query
                    ->where("psgc_code", $psgcCode)
                    ->where("geographic_level", "City")
                    ->orWhere(function ($q) use ($psgcCode) {
                        $q->where("psgc_code", $psgcCode)->where(
                            "geographic_level",
                            "Mun",
                        );
                    });
            })->first();

            if (!$municity) {
                return PSGCHelper::formatErrorResponse(
                    "City/Municipality not found",
                    404,
                );
            }

            $barangayIdentifier = PSGCHelper::extractBarangayIdentifier(
                $psgcCode,
            );
            $perPage = $request->input("per_page", 15);

            // Get barangays matching the city/municipality's barangay identifier
            $data = Q12026::barangays()
                ->where(
                    "barangay_identifier",
                    "like",
                    $barangayIdentifier . "%",
                )
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Barangays retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error(
                "City/Municipality Barangays Error: " . $e->getMessage(),
            );
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve barangays",
                500,
            );
        }
    }


    #[
        OA\Get(
            path: "/api/cities",
            summary: "Get all cities",
            description: "Retrieve all cities with pagination",
            tags: ["Cities"],
            parameters: [
                new OA\Parameter(
                    name: "page",
                    description: "Page number",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 1),
                ),
                new OA\Parameter(
                    name: "per_page",
                    description: "Records per page",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 15),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Cities retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function cities(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 15);
            $data = Q12026::cities()->paginate($perPage);
            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Cities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse("Failed to retrieve cities", 500);
        }
    }

    #[
        OA\Get(
            path: "/api/cities/{psgc_code}",
            summary: "Get specific city",
            description: "Retrieve a specific city by its PSGC code",
            tags: ["Cities"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "City PSGC code",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "string"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "City retrieved successfully",
                ),
                new OA\Response(response: 404, description: "City not found"),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function city(string $psgcCode)
    {
        try {
            $record = Q12026::cities()->where("psgc_code", $psgcCode)->first();

            if (!$record) {
                return PSGCHelper::formatErrorResponse("City not found", 404);
            }

            return PSGCHelper::formatResponse(
                $record,
                "City retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("City Show Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse("Failed to retrieve city", 500);
        }
    }

    #[
        OA\Get(
            path: "/api/cities/classification/highly-urbanized",
            summary: "Get highly urbanized cities",
            description: "Retrieve all highly urbanized cities",
            tags: ["Cities"],
            parameters: [
                new OA\Parameter(
                    name: "page",
                    description: "Page number",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 1),
                ),
                new OA\Parameter(
                    name: "per_page",
                    description: "Records per page",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 15),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Highly urbanized cities retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function highly_urbanized_cities(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 15);
            $data = Q12026::cities()
                ->where("city_classification", "like", "%HUC%")
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Highly urbanized cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("HUC Cities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve highly urbanized cities",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/cities/classification/component",
            summary: "Get component cities",
            description: "Retrieve all component cities",
            tags: ["Cities"],
            parameters: [
                new OA\Parameter(
                    name: "page",
                    description: "Page number",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 1),
                ),
                new OA\Parameter(
                    name: "per_page",
                    description: "Records per page",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 15),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Component cities retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function component_cities(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 15);
            $data = Q12026::cities()
                ->where("city_classification", "Component City")
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Component cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Component Cities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve component cities",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/cities/classification/independent-component",
            summary: "Get independent component cities",
            description: "Retrieve all independent component cities",
            tags: ["Cities"],
            parameters: [
                new OA\Parameter(
                    name: "page",
                    description: "Page number",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 1),
                ),
                new OA\Parameter(
                    name: "per_page",
                    description: "Records per page",
                    in: "query",
                    required: false,
                    schema: new OA\Schema(type: "integer", default: 15),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Independent component cities retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function independent_component_cities(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 15);
            $data = Q12026::cities()
                ->where("city_classification", "Independent Component City")
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Independent component cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error(
                "Independent Component Cities Error: " . $e->getMessage(),
            );
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve independent component cities",
                500,
            );
        }
    }
}
