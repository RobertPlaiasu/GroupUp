<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProjectController extends Controller
{
    use ProjectTrait;
    
    public function index()
    {
        try{
            return response()->json([
                'status' => true,
                'projects' => Project::where('user_id',Auth::id())->get(),
            ], 200);
        }
        catch(Throwable $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $validate = Validator::make($request->all(),[
                'name' => 'required|string|max:100|min:5',
                'start' => 'required|date|after:today',
                'end' => 'required|date|after:today'               
            ]);

            if($validate->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors(), 
                ],401);
            }

            Project::create([
                'name' => $request->name,
                'start' => $request->start,
                'end' => $request->end,
                'pin' => rand(100000,999999),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Project created succesfully',
            ], 200);

        }
        catch(Throwable $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Project $project)
    {
        try{

            if($this->userUnauthorized($project) != null)
            {
                return $this->userUnauthorized($project);
            }

            return response()->json([
                'status' => true,
                'project' => $project,
            ], 200);
        }
        catch(Throwable $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    
    public function destroy(Project $project)
    {
        try {
            if($this->userUnauthorized($project) != null)
            {
                return $this->userUnauthorized($project);
            }

            $project->delete();
            $project->save();
            return response()->json([
                'status' => true, 
            ], 200);
        }
        catch(Throwable $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function enter(Request $request)
    {
        try {
            $validate = Validator::make($request->all(),[
                'pin' => 'required|integer|max:999999|min:100000',           
            ]);

            if($validate->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors(), 
                ],401);
            }

            $project = Project::where("pin",$request->pin)->first();
            if($project == null)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Pin doesn\'t exist!', 
                ],401); 
            }
            if($request->cookie("project-" . $project->id))
            {
                return response()->json([
                    'status' => false,
                    'message' => "You already entered in the project!"
                ],401);
            }

            return response()->json([
                'status' => true, 
            ], 200)->withCookie(cookie()->forever("project-" . $project->id,$project->pin));

        }
        catch(Throwable $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
