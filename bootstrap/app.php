<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                $model = class_basename($e->getModel());

                $status = 404;
                return response()->json([
                    'message' => "{$model} not found",
                    'status' => $status,
                ], $status);
            }
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                $status = 401;
                return response()->json([
                    'message' => 'Unauthenticated',
                    'status' => $status,
                ], $status);
            }
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                $status = 422;
                return response()->json([
                    'message' => 'Validation failed',
                    'status' => $status,
                    'errors' => $e->errors(),
                ], $status);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($request->is('api/*')) {
                $status = $e->getStatusCode();
                return response()->json([
                    'message' => $e->getMessage() ?: 'HTTP error',
                    'status' => $status,
                ], $status);
            }
        });

    })
    ->create();
