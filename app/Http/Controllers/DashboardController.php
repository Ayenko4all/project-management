<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::count();
        $employees = Employee::count();
        $projectsByStatus = Project::select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        return response()->json([
            "status" => "success",
            "status_code" => Response::HTTP_OK,
            "data" => [
                "projects_by_status" => $projectsByStatus,
                "no_of_projects" => $projects,
                "no_of_employees" => $employees,
            ]
        ], Response::HTTP_OK);
    }
}
