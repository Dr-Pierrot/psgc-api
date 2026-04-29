<?php

namespace App\Http\Controllers\API\PSGC_new\Q12026;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Models\PSGC_new\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Support\Facades\Log;


class PSGCController extends Controller
{
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
            $perPage = $request->input("per_page", 15);
            $data = Q12026::paginate($perPage);
            return response()->json(PSGCHelper::formatPaginatedResponse($data));
        } catch (\Exception $e) {
            Log::error("PSGC Index Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
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
            $query = $request->input("q");

            if (!$query) {
                return PSGCHelper::formatErrorResponse(
                    "Search query is required",
                    400,
                );
            }

            $perPage = $request->input("per_page", 15);
            $data = Q12026::where("name", "like", "%" . $query . "%")
                ->orWhere("psgc_code", "like", "%" . $query . "%")
                ->paginate($perPage);

            return response()->json(
                PSGCHelper::formatPaginatedResponse(
                    $data,
                    "Search results retrieved successfully",
                ),
            );
        } catch (\Exception $e) {
            Log::error("PSGC Search Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse("Search failed", 500);
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
    public function show(string $psgcCode)
    {
        try {
            $record = Q12026::where("psgc_code", $psgcCode)->first();

            if (!$record) {
                return PSGCHelper::formatErrorResponse("Record not found", 404);
            }

            return PSGCHelper::formatResponse(
                $record,
                "Record retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("PSGC Show Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse("Failed to retrieve record", 500);
        }
    }
}
