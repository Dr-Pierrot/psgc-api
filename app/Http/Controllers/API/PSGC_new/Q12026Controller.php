<?php

namespace App\Http\Controllers\API\PSGC_new;

use App\Http\Controllers\Controller;
use App\Models\PSGC_new\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class Q12026Controller extends Controller
{
    

    #[
        OA\Get(
            path: "/api/provinces",
            summary: "Get all provinces",
            description: "Retrieve all provinces with pagination",
            tags: ["Provinces"],
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
                    description: "Provinces retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function provinces(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 15);
            $data = Q12026::provinces()->paginate($perPage);
            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Provinces retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Provinces Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve provinces",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/provinces/{psgc_code}",
            summary: "Get specific province",
            description: "Retrieve a specific province by its PSGC code",
            tags: ["Provinces"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Province PSGC code",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "string"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Province retrieved successfully",
                ),
                new OA\Response(
                    response: 404,
                    description: "Province not found",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function province($psgcCode)
    {
        try {
            $record = Q12026::provinces()
                ->where("psgc_code", $psgcCode)
                ->first();

            if (!$record) {
                return PSGCHelper::formatErrorResponse("Province not found", 404);
            }

            return PSGCHelper::formatResponse(
                $record,
                "Province retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Province Show Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve province",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/provinces/{psgc_code}/cities",
            summary: "Get cities/municipalities in a province",
            description: "Retrieve all cities and municipalities in a specific province using PSGC code",
            tags: ["Provinces"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Province PSGC code",
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
                    description: "Cities/Municipalities retrieved successfully",
                ),
                new OA\Response(
                    response: 404,
                    description: "Province not found",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function province_cities($psgcCode, Request $request)
    {
        try {
            $province = Q12026::provinces()
                ->where("psgc_code", $psgcCode)
                ->first();

            if (!$province) {
                return PSGCHelper::formatErrorResponse("Province not found", 404);
            }

            $munCityIdentifier = PSGCHelper::extractMunCityIdentifier(
                $psgcCode,
            );
            $perPage = $request->input("per_page", 15);

            // Get both cities and municipalities for this province
            $citiesQuery = Q12026::cities()->where(
                "mun_city_identifier",
                "like",
                $munCityIdentifier . "%",
            );
            $municipalitiesQuery = Q12026::municipalities()->where(
                "mun_city_identifier",
                "like",
                $munCityIdentifier . "%",
            );

            // Combine results and paginate
            $data = $citiesQuery
                ->union($municipalitiesQuery)
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Cities/Municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Province Cities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve cities/municipalities",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/provinces/{psgc_code}/barangays",
            summary: "Get barangays in a province",
            description: "Retrieve all barangays in a specific province using PSGC code",
            tags: ["Provinces"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Province PSGC code",
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
                    description: "Province not found",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function province_barangays($psgcCode, Request $request)
    {
        try {
            $province = Q12026::provinces()
                ->where("psgc_code", $psgcCode)
                ->first();

            if (!$province) {
                return PSGCHelper::formatErrorResponse("Province not found", 404);
            }

            $barangayIdentifier = PSGCHelper::extractBarangayIdentifier(
                $psgcCode,
            );
            $perPage = $request->input("per_page", 15);

            // Get barangays matching the province's barangay identifier
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
            Log::error("Province Barangays Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve barangays",
                500,
            );
        }
    }

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
    public function municity_barangays($psgcCode, Request $request)
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
    public function city($psgcCode)
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

    #[
        OA\Get(
            path: "/api/municipalities",
            summary: "Get all municipalities",
            description: "Retrieve all municipalities with pagination",
            tags: ["Municipalities"],
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
                    description: "Municipalities retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function municipalities(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 15);
            $data = Q12026::municipalities()->paginate($perPage);
            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Municipalities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve municipalities",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/municipalities/{psgc_code}",
            summary: "Get specific municipality",
            description: "Retrieve a specific municipality by its PSGC code",
            tags: ["Municipalities"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Municipality PSGC code",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "string"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Municipality retrieved successfully",
                ),
                new OA\Response(
                    response: 404,
                    description: "Municipality not found",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function municipality($psgcCode)
    {
        try {
            $record = Q12026::municipalities()
                ->where("psgc_code", $psgcCode)
                ->first();

            if (!$record) {
                return PSGCHelper::formatErrorResponse(
                    "Municipality not found",
                    404,
                );
            }

            return PSGCHelper::formatResponse(
                $record,
                "Municipality retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Municipality Show Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve municipality",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/sub-municipalities",
            summary: "Get all sub-municipalities",
            description: "Retrieve all sub-municipalities with pagination",
            tags: ["Sub-Municipalities"],
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
                    description: "Sub-municipalities retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function sub_municipalities(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 15);
            $data = Q12026::subMunicipalities()->paginate($perPage);
            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Sub-municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Sub-municipalities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve sub-municipalities",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/sub-municipalities/{psgc_code}",
            summary: "Get specific sub-municipality",
            description: "Retrieve a specific sub-municipality by its PSGC code",
            tags: ["Sub-Municipalities"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Sub-municipality PSGC code",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "string"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Sub-municipality retrieved successfully",
                ),
                new OA\Response(
                    response: 404,
                    description: "Sub-municipality not found",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function sub_municipality($psgcCode)
    {
        try {
            $record = Q12026::subMunicipalities()
                ->where("psgc_code", $psgcCode)
                ->first();

            if (!$record) {
                return PSGCHelper::formatErrorResponse(
                    "Sub-municipality not found",
                    404,
                );
            }

            return PSGCHelper::formatResponse(
                $record,
                "Sub-municipality retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Sub-municipality Show Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve sub-municipality",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/barangays",
            summary: "Get all barangays",
            description: "Retrieve all barangays with pagination",
            tags: ["Barangays"],
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
                    description: "Barangays retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function barangays(Request $request)
    {
        try {
            $perPage = $request->input("per_page", 15);
            $data = Q12026::barangays()->paginate($perPage);
            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Barangays retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Barangays Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve barangays",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/barangays/{psgc_code}",
            summary: "Get specific barangay",
            description: "Retrieve a specific barangay by its PSGC code",
            tags: ["Barangays"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Barangay PSGC code",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "string"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Barangay retrieved successfully",
                ),
                new OA\Response(
                    response: 404,
                    description: "Barangay not found",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function barangay($psgcCode)
    {
        try {
            $record = Q12026::barangays()
                ->where("psgc_code", $psgcCode)
                ->first();

            if (!$record) {
                return PSGCHelper::formatErrorResponse("Barangay not found", 404);
            }

            return PSGCHelper::formatResponse(
                $record,
                "Barangay retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Barangay Show Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve barangay",
                500,
            );
        }
    }

    // Legacy methods kept for backward compatibility
    public function geographic_level()
    {
        return $this->geographic_levels();
    }

    public function city_classification()
    {
        return $this->city_classifications();
    }

    public function income_classification()
    {
        return $this->income_classifications();
    }
}
