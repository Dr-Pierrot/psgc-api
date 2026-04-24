<?php

namespace App\Http\Controllers\API\PSGC;

use App\Http\Controllers\Controller;
use App\Models\PSGC\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class Q12026Controller extends Controller
{
    /**
     * Helper function to format paginated response
     */
    private function formatPaginatedResponse(
        $data,
        $message = "Data retrieved successfully",
    ) {
        return [
            "response_code" => 200,
            "status" => "success",
            "message" => $message,
            "data" => $data->items(),
            "pagination" => [
                "current_page" => $data->currentPage(),
                "total_pages" => $data->lastPage(),
                "per_page" => $data->perPage(),
                "total" => $data->total(),
            ],
        ];
    }

    /**
     * Helper function to format non-paginated response
     */
    private function formatResponse(
        $data,
        $message = "Data retrieved successfully",
        $responseCode = 200,
    ) {
        return response()->json(
            [
                "response_code" => $responseCode,
                "status" => "success",
                "message" => $message,
                "data" => $data,
            ],
            $responseCode,
        );
    }

    /**
     * Helper function to format error response
     */
    private function formatErrorResponse(
        $message = "An error occurred",
        $responseCode = 500,
    ) {
        return response()->json(
            [
                "response_code" => $responseCode,
                "status" => "error",
                "message" => $message,
                "data" => null,
            ],
            $responseCode,
        );
    }

    #[
        OA\Get(
            path: "/api/psgc",
            summary: "List all PSGC records",
            description: "Retrieve all Philippine Standard Geographic Code records with pagination",
            tags: ["PSGC"],
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
                    description: "Records retrieved successfully",
                    content: new OA\JsonContent(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "response_code",
                                type: "integer",
                                example: 200,
                            ),
                            new OA\Property(
                                property: "status",
                                type: "string",
                                example: "success",
                            ),
                            new OA\Property(
                                property: "message",
                                type: "string",
                                example: "Data retrieved successfully",
                            ),
                            new OA\Property(
                                property: "data",
                                type: "array",
                                items: new OA\Items(type: "object"),
                            ),
                            new OA\Property(
                                property: "pagination",
                                type: "object",
                            ),
                        ],
                    ),
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function index(Request $request)
    {
        try {
            $perPage = $request->get("per_page", 15);
            $data = Q12026::paginate($perPage);
            return response()->json($this->formatPaginatedResponse($data));
        } catch (\Exception $e) {
            Log::error("PSGC Index Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve PSGC records",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/psgc/search",
            summary: "Search PSGC records",
            description: "Search PSGC records by name or PSGC code",
            tags: ["PSGC"],
            parameters: [
                new OA\Parameter(
                    name: "q",
                    description: "Search query (name or PSGC code)",
                    in: "query",
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
                    description: "Search results retrieved successfully",
                ),
                new OA\Response(
                    response: 400,
                    description: "Search query is required",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function search(Request $request)
    {
        try {
            $query = $request->get("q");

            if (!$query) {
                return $this->formatErrorResponse(
                    "Search query is required",
                    400,
                );
            }

            $perPage = $request->get("per_page", 15);
            $data = Q12026::where("name", "like", "%" . $query . "%")
                ->orWhere("psgc_code", "like", "%" . $query . "%")
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Search results retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("PSGC Search Error: " . $e->getMessage());
            return $this->formatErrorResponse("Search failed", 500);
        }
    }

    #[
        OA\Get(
            path: "/api/psgc/{psgc_code}",
            summary: "Get specific PSGC record",
            description: "Retrieve a specific PSGC record by its code",
            tags: ["PSGC"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "PSGC code",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "string"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Record retrieved successfully",
                ),
                new OA\Response(response: 404, description: "Record not found"),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function show($psgcCode)
    {
        try {
            $record = Q12026::where("psgc_code", $psgcCode)->first();

            if (!$record) {
                return $this->formatErrorResponse("Record not found", 404);
            }

            return $this->formatResponse(
                $record,
                "Record retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("PSGC Show Error: " . $e->getMessage());
            return $this->formatErrorResponse("Failed to retrieve record", 500);
        }
    }

    #[
        OA\Get(
            path: "/api/geographic-levels",
            summary: "Get all geographic levels",
            description: "Retrieve all distinct geographic levels",
            tags: ["Classification"],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Geographic levels retrieved successfully",
                    content: new OA\JsonContent(
                        type: "object",
                        properties: [
                            new OA\Property(
                                property: "response_code",
                                type: "integer",
                                example: 200,
                            ),
                            new OA\Property(
                                property: "status",
                                type: "string",
                                example: "success",
                            ),
                            new OA\Property(
                                property: "message",
                                type: "string",
                            ),
                            new OA\Property(
                                property: "data",
                                type: "array",
                                items: new OA\Items(type: "string"),
                            ),
                        ],
                    ),
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function geographic_levels()
    {
        try {
            $levels = Q12026::getGeographicLevels();
            return $this->formatResponse(
                $levels,
                "Geographic levels retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Geographic Levels Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve geographic levels",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/city-classifications",
            summary: "Get all city classifications",
            description: "Retrieve all distinct city classifications",
            tags: ["Classification"],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "City classifications retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function city_classifications()
    {
        try {
            $classifications = Q12026::getCityClassifications();
            return $this->formatResponse(
                $classifications,
                "City classifications retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("City Classifications Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve city classifications",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/income-classifications",
            summary: "Get all income classifications",
            description: "Retrieve all distinct income classifications",
            tags: ["Classification"],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Income classifications retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function income_classifications()
    {
        try {
            $classifications = Q12026::getIncomeClassifications();
            return $this->formatResponse(
                $classifications,
                "Income classifications retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Income Classifications Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve income classifications",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/urban-rural-classifications",
            summary: "Get all urban/rural classifications",
            description: "Retrieve all distinct urban/rural classifications",
            tags: ["Classification"],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Urban/rural classifications retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function urban_rural_classifications()
    {
        try {
            $classifications = Q12026::getUrbanRuralClassifications();
            return $this->formatResponse(
                $classifications,
                "Urban/rural classifications retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error(
                "Urban/Rural Classifications Error: " . $e->getMessage(),
            );
            return $this->formatErrorResponse(
                "Failed to retrieve urban/rural classifications",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/regions",
            summary: "Get all regions",
            description: "Retrieve all regions with pagination",
            tags: ["Regions"],
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
                    description: "Regions retrieved successfully",
                ),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function regions(Request $request)
    {
        try {
            $perPage = $request->get("per_page", 15);
            $data = Q12026::regions()->paginate($perPage);
            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Regions retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Regions Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve regions",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/regions/{psgc_code}",
            summary: "Get specific region",
            description: "Retrieve a specific region by its PSGC code",
            tags: ["Regions"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Region PSGC code",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "string"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Region retrieved successfully",
                ),
                new OA\Response(response: 404, description: "Region not found"),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function region($psgcCode)
    {
        try {
            $record = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$record) {
                return $this->formatErrorResponse("Region not found", 404);
            }

            return $this->formatResponse(
                $record,
                "Region retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Region Show Error: " . $e->getMessage());
            return $this->formatErrorResponse("Failed to retrieve region", 500);
        }
    }

    #[
        OA\Get(
            path: "/api/regions/{psgc_code}/provinces",
            summary: "Get provinces in a region",
            description: "Retrieve all provinces in a specific region",
            tags: ["Regions"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Region PSGC code",
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
                    description: "Provinces retrieved successfully",
                ),
                new OA\Response(response: 404, description: "Region not found"),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function region_provinces($psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return $this->formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->get("per_page", 15);
            $data = Q12026::provinces()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Provinces retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Provinces Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve provinces",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/regions/{psgc_code}/cities",
            summary: "Get cities in a region",
            description: "Retrieve all cities in a specific region",
            tags: ["Regions"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Region PSGC code",
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
                    description: "Cities retrieved successfully",
                ),
                new OA\Response(response: 404, description: "Region not found"),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function region_cities($psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return $this->formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->get("per_page", 15);
            $data = Q12026::cities()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Cities Error: " . $e->getMessage());
            return $this->formatErrorResponse("Failed to retrieve cities", 500);
        }
    }

    #[
        OA\Get(
            path: "/api/regions/{psgc_code}/municipalities",
            summary: "Get municipalities in a region",
            description: "Retrieve all municipalities in a specific region",
            tags: ["Regions"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Region PSGC code",
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
                    description: "Municipalities retrieved successfully",
                ),
                new OA\Response(response: 404, description: "Region not found"),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function region_municipalities($psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return $this->formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->get("per_page", 15);
            $data = Q12026::municipalities()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Municipalities Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve municipalities",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/regions/{psgc_code}/sub-municipalities",
            summary: "Get sub-municipalities in a region",
            description: "Retrieve all sub-municipalities in a specific region",
            tags: ["Regions"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Region PSGC code",
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
                    description: "Sub-municipalities retrieved successfully",
                ),
                new OA\Response(response: 404, description: "Region not found"),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function region_submunicipalities($psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return $this->formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->get("per_page", 15);
            $data = Q12026::subMunicipalities()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Sub-municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Sub-municipalities Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve sub-municipalities",
                500,
            );
        }
    }

    #[
        OA\Get(
            path: "/api/regions/{psgc_code}/barangays",
            summary: "Get barangays in a region",
            description: "Retrieve all barangays in a specific region",
            tags: ["Regions"],
            parameters: [
                new OA\Parameter(
                    name: "psgc_code",
                    description: "Region PSGC code",
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
                new OA\Response(response: 404, description: "Region not found"),
                new OA\Response(
                    response: 500,
                    description: "Internal server error",
                ),
            ],
        ),
    ]
    public function region_barangays($psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return $this->formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->get("per_page", 15);
            $data = Q12026::barangays()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Barangays retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Barangays Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve barangays",
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
                return $this->formatErrorResponse("Province not found", 404);
            }

            $munCityIdentifier = PSGCHelper::extractMunCityIdentifier(
                $psgcCode,
            );
            $perPage = $request->get("per_page", 15);

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
                $this->formatPaginatedResponse(
                    $data,
                    "Cities/Municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Province Cities Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
                return $this->formatErrorResponse("Province not found", 404);
            }

            $barangayIdentifier = PSGCHelper::extractBarangayIdentifier(
                $psgcCode,
            );
            $perPage = $request->get("per_page", 15);

            // Get barangays matching the province's barangay identifier
            $data = Q12026::barangays()
                ->where(
                    "barangay_identifier",
                    "like",
                    $barangayIdentifier . "%",
                )
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Barangays retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Province Barangays Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
                return $this->formatErrorResponse(
                    "City/Municipality not found",
                    404,
                );
            }

            $barangayIdentifier = PSGCHelper::extractBarangayIdentifier(
                $psgcCode,
            );
            $perPage = $request->get("per_page", 15);

            // Get barangays matching the city/municipality's barangay identifier
            $data = Q12026::barangays()
                ->where(
                    "barangay_identifier",
                    "like",
                    $barangayIdentifier . "%",
                )
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Barangays retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error(
                "City/Municipality Barangays Error: " . $e->getMessage(),
            );
            return $this->formatErrorResponse(
                "Failed to retrieve barangays",
                500,
            );
        }
    }

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
            $perPage = $request->get("per_page", 15);
            $data = Q12026::provinces()->paginate($perPage);
            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Provinces retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Provinces Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
                return $this->formatErrorResponse("Province not found", 404);
            }

            return $this->formatResponse(
                $record,
                "Province retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Province Show Error: " . $e->getMessage());
            return $this->formatErrorResponse(
                "Failed to retrieve province",
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
            $perPage = $request->get("per_page", 15);
            $data = Q12026::cities()->paginate($perPage);
            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Cities Error: " . $e->getMessage());
            return $this->formatErrorResponse("Failed to retrieve cities", 500);
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
                return $this->formatErrorResponse("City not found", 404);
            }

            return $this->formatResponse(
                $record,
                "City retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("City Show Error: " . $e->getMessage());
            return $this->formatErrorResponse("Failed to retrieve city", 500);
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
            $perPage = $request->get("per_page", 15);
            $data = Q12026::cities()
                ->where("city_classification", "like", "%HUC%")
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Highly urbanized cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("HUC Cities Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
            $perPage = $request->get("per_page", 15);
            $data = Q12026::cities()
                ->where("city_classification", "Component City")
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Component cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Component Cities Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
            $perPage = $request->get("per_page", 15);
            $data = Q12026::cities()
                ->where("city_classification", "Independent Component City")
                ->paginate($perPage);

            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Independent component cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error(
                "Independent Component Cities Error: " . $e->getMessage(),
            );
            return $this->formatErrorResponse(
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
            $perPage = $request->get("per_page", 15);
            $data = Q12026::municipalities()->paginate($perPage);
            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Municipalities Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
                return $this->formatErrorResponse(
                    "Municipality not found",
                    404,
                );
            }

            return $this->formatResponse(
                $record,
                "Municipality retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Municipality Show Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
            $perPage = $request->get("per_page", 15);
            $data = Q12026::subMunicipalities()->paginate($perPage);
            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Sub-municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Sub-municipalities Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
                return $this->formatErrorResponse(
                    "Sub-municipality not found",
                    404,
                );
            }

            return $this->formatResponse(
                $record,
                "Sub-municipality retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Sub-municipality Show Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
            $perPage = $request->get("per_page", 15);
            $data = Q12026::barangays()->paginate($perPage);
            return response()->json(
                $this->formatPaginatedResponse(
                    $data,
                    "Barangays retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Barangays Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
                return $this->formatErrorResponse("Barangay not found", 404);
            }

            return $this->formatResponse(
                $record,
                "Barangay retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Barangay Show Error: " . $e->getMessage());
            return $this->formatErrorResponse(
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
