<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProposalController extends Controller
{
    
    public function index(Subject $subject)
    {
        try{
            if($this->userUnauthorized($subject->project) != null)
            {
                return $this->userUnauthorized($subject->project);
            }

            return response()->json([
                'status' => true,
                'subjects' => Proposal::where('subject_id',$subject->id)->get(),
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

   
    public function create(Subject $subject,Request $request)
    {
        try{
            if($request->cookie('project-' . $subject->project->id) != $subject->project->pin)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
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

    
    public function store(Subject $subject,Request $request)
    {
        try
        {
            if($request->cookie('project-' . $subject->project->id) != $subject->project->pin)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validate = Validator::make($request->all(),[
                'name' => 'required|string|max:100',
                'url' => 'required|active_url',
                'text' => 'nullable|string',
                'price' => 'required|integer'
            ]);

            if($validate->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors(), 
                ],401);
            }
            
            Proposal::create([
                'name' => $request->name,
                'url' => $request->url,
                'text' => $request->text,
                'price' => $request->price,
                'subject_id' => $subject->id,
            ]);
            

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

    
    public function show(Proposal $proposal)
    {
        //
    }

    
    public function edit(Proposal $proposal)
    {
        //
    }

    
    public function update(Request $request, Proposal $proposal)
    {
        //
    }

    
    public function destroy(Proposal $proposal)
    {
        //
    }
}
