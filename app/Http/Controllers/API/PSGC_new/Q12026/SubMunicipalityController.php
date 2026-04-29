<?php

namespace App\Http\Controllers\API\PSGC_new\Q12026;

use App\Http\Controllers\Controller;
use App\Models\PSGC_new\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class SubMunicipalityController extends Controller
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
    public function sub_municipality(string $psgcCode)
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
}
