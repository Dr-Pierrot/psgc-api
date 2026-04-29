<?php

namespace App\Http\Controllers\API\PSGC_new\Q12026;

use App\Http\Controllers\Controller;
use App\Models\PSGC_new\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class RegionController extends Controller
{
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
            $perPage = $request->input("per_page", 15);
            $data = Q12026::regions()->paginate($perPage);
            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Regions retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Regions Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
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
    public function region(string $psgcCode)
    {
        try {
            $record = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$record) {
                return PSGCHelper::formatErrorResponse("Region not found", 404);
            }

            return PSGCHelper::formatResponse(
                $record,
                "Region retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Region Show Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse("Failed to retrieve region", 500);
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
    public function region_provinces(string $psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return PSGCHelper::formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->input("per_page", 15);
            $data = Q12026::provinces()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Provinces retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Provinces Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
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
    public function region_cities(string $psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return PSGCHelper::formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->input("per_page", 15);
            $data = Q12026::cities()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Cities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Cities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse("Failed to retrieve cities", 500);
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
    public function region_municipalities(string $psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return PSGCHelper::formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->input("per_page", 15);
            $data = Q12026::municipalities()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Municipalities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
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
    public function region_submunicipalities(string $psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return PSGCHelper::formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->input("per_page", 15);
            $data = Q12026::subMunicipalities()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Sub-municipalities retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Sub-municipalities Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
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
    public function region_barangays(string $psgcCode, Request $request)
    {
        try {
            $region = Q12026::regions()->where("psgc_code", $psgcCode)->first();

            if (!$region) {
                return PSGCHelper::formatErrorResponse("Region not found", 404);
            }

            $regionCode = PSGCHelper::extractRegionCode($psgcCode);
            $perPage = $request->input("per_page", 15);
            $data = Q12026::barangays()
                ->byRegionCode($regionCode)
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Barangays retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("Region Barangays Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve barangays",
                500,
            );
        }
    }
}
