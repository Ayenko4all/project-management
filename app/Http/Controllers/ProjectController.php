<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $name = $request->query('name');
        $status = $request->query('status');
        $cacheKey = "projects_list";
        $projects = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($name, $status) {
            return Project::with('employees')
                ->when($name, function ($query, $name) {
                    $query->where('name', 'like', "%$name%");
                })
                ->when($status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->paginate(10);
        });

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "data" => ["projects" => $projects]
        ], Response::HTTP_OK);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            $project = Project::create([
                "name" => $request->validated('name'),
                "author_id" => auth()->id(),
                "description" => $request->validated('description'),
                "start_date" => $request->validated('start_date'),
                "end_date" => $request->validated('end_date'),
                "status" => $request->validated('status'),
            ]);

            return response()->json([
                "status" => "success",
                "status_code" => Response::HTTP_CREATED,
                "data" => ["project" => $project->load('employees')]
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
        if (!$project = Project::query()->where('id', $id)->first()){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_NOT_FOUND,
                "error" => [
                    "message" => "Project not found."
                ]
            ],  Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "data" => ["projects" => $project->load('employees')]
        ], Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     * @param UpdateProjectRequest $request
     * @param string $id
     * @return mixed
     */
    public function update(UpdateProjectRequest $request, string $id): mixed
    {
        try {
            $project = Project::query()->where("id", $id)->first();


            Gate::authorize('update', $project);

            if(! $project){
                return response()->json([
                    "status" => "failed",
                    "status_code" => Response::HTTP_NOT_FOUND,
                    "error" => [
                        "message" => "Project not found."
                    ]
                ],  Response::HTTP_NOT_FOUND);
            }

            $project->update([
                "name" => $request->validated('name'),
                "description" => $request->validated('description'),
                "start_date" => $request->validated('start_date'),
                "end_date" => $request->validated('end_date'),
                "status" => $request->validated('status'),
            ]);

            return response()->json([
                "status" => "success",
                "status_code" => Response::HTTP_OK,
                "data" => ["project" => $project]
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
        if (!$project = Project::query()->where('id', $id)->first()){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_NOT_FOUND,
                "error" => [
                    "message" => "Project not found."
                ]
            ],  Response::HTTP_NOT_FOUND);
        }

        Gate::authorize('delete', $project);

        if($project->employees()->exists()) {
            foreach ($project->employees as $employee) {
                $employee->delete();
            }
        }

        $project->delete();

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
        $project = Project::withTrashed()
            ->where('id', $id)
            ->first();

        if (! $project){
            return response()->json([
                "status" => "failed",
                "status_code" => Response::HTTP_NOT_FOUND,
                "error" => [
                    "message" => "Project not found."
                ]
            ],  Response::HTTP_NOT_FOUND);
        }

        $project->restore();

        if($project->employees()->withTrashed()->exists()) {
            foreach ($project->employees as $employee) {
                $employee->restore();
            }
        }

        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_NO_CONTENT
        ], Response::HTTP_NO_CONTENT);
    }
}
