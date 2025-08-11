<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {

            if ($e instanceof ModelNotFoundException) {
                return $this->errorResponse(
                    __('Resource not found.'),
                    Response::HTTP_NOT_FOUND
                );
            }

            if ($e instanceof AuthorizationException) {
                return $this->errorResponse(
                    __('You are not authorized to perform this action.'),
                    Response::HTTP_FORBIDDEN
                );
            }

            if ($e instanceof ValidationException) {
                return $this->errorResponse(
                    __('Validation failed.'),
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $e->errors()
                );
            }

            
            Log::error('Unexpected error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);

            return $this->errorResponse(
                __('Something went wrong.'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return parent::render($request, $e);
    }
}
