<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', 'api');
Route::prefix('api')->group(function () {
    Route::get('/', function () {
        return response()->json([
            "status" => "success",
            "data" => ["message" => "Welcome to project management API"]
        ]);
    });
});
