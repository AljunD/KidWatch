<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller as LaravelController;

/**
 * Base API Controller
 *
 * Provides standardized JSON responses for all API endpoints.
 * Ensures consistent structure for success and error cases.
 */
abstract class Controller extends LaravelController
{
    /**
     * Return a standardized success JSON response.
     *
     * @param mixed       $data    The payload to return (array, object, or null).
     * @param string      $message Success message (default: "Success").
     * @param int         $status  HTTP status code (default: 200).
     * @param array       $meta    Optional metadata (pagination, extra info).
     *
     * @return JsonResponse
     */
    protected function successResponse(
        mixed $data,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
        ], $status);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param string $message Error message.
     * @param int    $status  HTTP status code (default: 400).
     * @param array  $errors  Validation or custom error details.
     *
     * @return JsonResponse
     */
    protected function errorResponse(
        string $message,
        int $status = 400,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
