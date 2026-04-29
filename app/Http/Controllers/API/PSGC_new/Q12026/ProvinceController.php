<?php

namespace App\Http\Controllers\API\PSGC_new\Q12026;

use App\Http\Controllers\Controller;
use App\Models\PSGC_new\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class ProvinceController extends Controller
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
    public function province(string $psgcCode)
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
    public function province_cities(string $psgcCode, Request $request)
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
    public function province_barangays(string $psgcCode, Request $request)
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
}
