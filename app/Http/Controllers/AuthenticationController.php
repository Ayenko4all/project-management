<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticationRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function login(AuthenticationRequest $request)
    {
        $user = User::query()
            ->where('email', $request->validated('email'))
            ->first();

        if (! $user || !Hash::check($request->validated('password'), $user->password)){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                "error" => [
                    "message" => trans('auth.failed')
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $user->createToken(config('auth.token_name'), ['*'])->plainTextToken;

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "data" => ["access_token" => $token]
        ], Response::HTTP_OK);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            "name" => $request->validated('name'),
            "email" => $request->validated('email'),
            "password" => Hash::make($request->validated('email'))
        ]);

        $user->notify(new WelcomeNotification());

        $token = $user->createToken(config('auth.token_name'), ['*'])->plainTextToken;

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "data" => ["access_token" => $token]
        ], Response::HTTP_OK);
    }
}
