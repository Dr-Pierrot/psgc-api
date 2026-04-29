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
