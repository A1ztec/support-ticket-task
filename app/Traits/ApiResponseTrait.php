<?php

namespace App\Traits;

use App\Enums\System\ApiStatus;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return success response
     */
    public function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => ApiStatus::SUCCESS->value,
            'message' => $message,
            'data' => $data,
            'code' => $code
        ], $code);
    }

    /**
     * Return error response
     */
    public function errorResponse(string $message = 'Error occurred', int $code = 500, $errors = null): JsonResponse
    {
        $response = [
            'status' => ApiStatus::ERROR->value,
            'message' => $message,
            'code' => $code
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return validation error response
     */
    public function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'status' => ApiStatus::VALIDATION_FAILED->value,
            'message' => $message,
            'errors' => $errors,
            'code' => 422
        ], 422);
    }

    /**
     * Return not found response
     */
    public function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'status' => ApiStatus::NOT_FOUND->value,
            'message' => $message,
            'code' => 404
        ], 404);
    }

    /**
     * Return unauthorized response
     */
    public function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'status' => ApiStatus::UNAUTHORIZED->value,
            'message' => $message,
            'code' => 401
        ], 401);
    }
}
