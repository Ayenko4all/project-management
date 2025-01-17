<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = Employee::with('project')->get();
        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "data" => ["employees" => $employees]
        ], Response::HTTP_OK);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            $employee = Employee::create([
                "name" => $request->validated('name'),
                "email" => $request->validated('email'),
                "project_id" => $request->validated('project_id'),
                "position" => $request->validated('position')
            ]);

            return response()->json([
                "status" => "success",
                "status_code" => Response::HTTP_CREATED,
                "data" => ["employee" => $employee->load('project')]
            ], Response::HTTP_CREATED);
        }catch (Throwable $throwable){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                "error" => [
                    "message" => $throwable->getMessage()
                ]
            ],  Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$employee = Employee::query()->where('id', $id)->first()){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_NOT_FOUND,
                "error" => [
                    "message" => "Employee not found."
                ]
            ],  Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "data" => ["employee" => $employee->load('project')]
        ], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, string $id)
    {
        try {
            $employee = Employee::query()->where("id", $id)->first();

            if(! $employee){
                return response()->json([
                    "status" => "failed",
                    "status_code" => Response::HTTP_NOT_FOUND,
                    "error" => [
                        "message" => "Employee not found."
                    ]
                ],  Response::HTTP_NOT_FOUND);
            }

            $employee->update([
                "name" => $request->validated('name'),
                "email" => $request->validated('email'),
                "project_id" => $request->validated('project_id'),
                "position" => $request->validated('position')
            ]);

            return response()->json([
                "status" => "success",
                "status_code" => Response::HTTP_OK,
                "data" => ["employee" => $employee]
            ], Response::HTTP_OK);
        }catch (Throwable $throwable){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_UNPROCESSABLE_ENTITY,
                "error" => [
                    "message" => $throwable->getMessage()
                ]
            ],  Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$employee = Employee::query()->where('id', $id)->first()){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_NOT_FOUND,
                "error" => [
                    "message" => "Employee not found."
                ]
            ],  Response::HTTP_NOT_FOUND);
        }

        $employee->delete();

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_NO_CONTENT
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(string $id)
    {
        $employee = Employee::withTrashed()
            ->where('id', $id)
            ->first();

        if (! $employee){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_NOT_FOUND,
                "error" => [
                    "message" => "Employee not found."
                ]
            ],  Response::HTTP_NOT_FOUND);
        }

        $employee->restore();

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_NO_CONTENT
        ], Response::HTTP_NO_CONTENT);
    }
}
