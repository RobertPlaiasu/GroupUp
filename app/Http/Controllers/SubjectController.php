<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SubjectController extends Controller
{
    use ProjectTrait;
    
    public function index(Project $project)
    {
        try{
            if($this->userUnauthorized($project) != null)
            {
                return $this->userUnauthorized($project);
            }

            return response()->json([
                'status' => true,
                'subjects' => Subject::where('project_id',$project->id)->get(),
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

    
    public function create(Project $project)
    {
        try{
            if($this->userUnauthorized($project) != null)
            {
                return $this->userUnauthorized($project);
            }

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

    
    public function store(Project $project,Request $request)
    {
        try
        {
            if($this->userUnauthorized($project) != null)
            {
                return $this->userUnauthorized($project);
            }

            $validate = Validator::make($request->all(),[
                'name' => 'required|array|max:100|min:2',
                'name.*' => 'string|max:100',              
            ]);

            if($validate->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors(), 
                ],401);
            }
            
            foreach($request->name as $name)
            {
                Subject::create([
                    'name' => $name,
                    'project_id' => $project->id,
                ]);
            }
            

            return response()->json([
                'status' => true,
                'message' => 'Subjects created succesfully',
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
}
