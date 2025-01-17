<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    public function assignRole(Request $request, User $user)
    {
        $role = Role::query()
            ->where('name', $request->input('role'))
            ->first();

        if (!$role) {
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_NOT_FOUND,
                "error" => [
                    "message" => "Role not found."
                ]
            ],  Response::HTTP_NOT_FOUND);
        }

        $user->roles()->syncWithoutDetaching($role);

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "message" => "Role assigned successfully"
        ], Response::HTTP_OK);
    }

    public function detachRole(Request $request, User $user)
    {
        $role = Role::query()
            ->where('name', $request->input('role'))
            ->first();

        if (!$role) {
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_NOT_FOUND,
                "error" => [
                    "message" => "Role not found."
                ]
            ],  Response::HTTP_NOT_FOUND);
        }

        $user->roles()->detach($role);

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "message" => "Role removed successfully"
        ], Response::HTTP_OK);
    }
}
