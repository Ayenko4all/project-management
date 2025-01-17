<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function (){
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request){
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    "status" => "fail",
                    "status_code" => Response::HTTP_UNAUTHORIZED,
                    "error" => [
                        "message" => $e->getMessage()
                    ]
                ], Response::HTTP_UNAUTHORIZED, []);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json([
                    "status" => "fail",
                    "status_code" => Response::HTTP_NOT_FOUND,
                    "error" => [
                        "message" => $e->getMessage()
                    ]
                ], Response::HTTP_NOT_FOUND, []);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    "status" => "fail",
                    "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                    "error" => [
                        "message" => $e->validator->errors()->all()
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY, []);
            }

            return response()->json([
                "status" => "fail",
                "status_code" => Response::HTTP_INTERNAL_SERVER_ERROR,
                "error" => [
                    "message" => "Something went wrong"
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR, []);
        });

    })->create();
