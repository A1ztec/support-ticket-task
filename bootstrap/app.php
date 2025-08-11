<?php

use App\Exceptions\Handler;
use App\Enums\System\ApiStatus;
use App\Traits\ApiResponseTrait;
use function Pest\Laravel\instance;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $api = new class {
            use ApiResponseTrait;
        };

        $exceptions->render(function (Throwable $e, $request) use ($api) {
            if ($request->expectsJson() || $request->is('api/*') || $request->wantsJson()) {
                if ($e instanceof ModelNotFoundException) {
                    return $api->errorResponse(__('Resource not found.'), ApiStatus::NOT_FOUND->httpCode());
                }

                if ($e instanceof NotFoundHttpException) {
                    return $api->errorResponse(__('Not found.'), ApiStatus::NOT_FOUND->httpCode());
                }

                if ($e instanceof AuthorizationException) {
                    return $api->errorResponse(__('You are not authorized to perform this action.'), ApiStatus::UNAUTHORIZED->httpCode());
                }

                if ($e instanceof ValidationException) {
                    return $api->validationErrorResponse($e->errors(), __('Validation failed.'));
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

                return $api->errorResponse(__('Something went wrong.'), ApiStatus::ERROR->httpCode());
            }
        });
    })->create();
