<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\RecommendationResource;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    public function index(): JsonResponse
    {
        $configs = \App\Models\RecommendationEngineConfig::all();
        return $this->successResponse(RecommendationResource::collection($configs));
    }
}
