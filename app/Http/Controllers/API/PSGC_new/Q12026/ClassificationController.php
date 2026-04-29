<?php

namespace App\Http\Controllers\API\PSGC_new\Q12026;

use App\Http\Controllers\Controller;
use App\Models\PSGC_new\Q12026;
use App\Traits\PSGCHelper;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class ClassificationController extends Controller
{
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
            return PSGCHelper::formatResponse(
                $levels,
                "Geographic levels retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Geographic Levels Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
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
            return PSGCHelper::formatResponse(
                $classifications,
                "City classifications retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("City Classifications Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
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
            return PSGCHelper::formatResponse(
                $classifications,
                "Income classifications retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error("Income Classifications Error: " . $e->getMessage());
            return PSGCHelper::formatErrorResponse(
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
            return PSGCHelper::formatResponse(
                $classifications,
                "Urban/rural classifications retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error(
                "Urban/Rural Classifications Error: " . $e->getMessage(),
            );
            return PSGCHelper::formatErrorResponse(
                "Failed to retrieve urban/rural classifications",
                500,
            );
        }
    }
}
