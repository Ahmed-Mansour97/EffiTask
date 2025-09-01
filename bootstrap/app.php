<?php

use App\Helpers\ApiResponse;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'JwtMiddleware' => JwtMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
            $exceptions->renderable(function (Throwable $e, $request) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return ApiResponse::send(null, 'Validation failed', Response::HTTP_UNPROCESSABLE_ENTITY, $e->errors());
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return ApiResponse::send(null, 'Resource not found', Response::HTTP_NOT_FOUND, 'Resource not found.');
            }

            if ($e instanceof HttpExceptionInterface) {
                return ApiResponse::send(null, 'error', $e->getStatusCode(), $e->getMessage(),);
            }
            
            return ApiResponse::send(null, 'Something went wrong.', Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(),);
        });
    })->create();
