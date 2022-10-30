<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

trait ProjectTrait
{
    public function userUnauthorized(Project $project)
    {
        if($project->user_id != Auth::id())
        {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return null;
    }
}
