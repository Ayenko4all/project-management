<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole($role)) {
            return response()->json([
                "status" => "fail",
                "status_code" => Response::HTTP_UNAUTHORIZED,
                "error" => [
                    "message" => "Unauthorized"
                ]
            ], Response::HTTP_UNAUTHORIZED, []);
        }

        return $next($request);
    }
}
