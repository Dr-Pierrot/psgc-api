<?php

namespace App\Http\Controllers\API\PSGC_new\Q12026;

use App\Http\Controllers\Controller;
use App\Models\PSGC_new\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class MunicipalityController extends Controller
{
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
    public function municipality(string $psgcCode)
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
}
