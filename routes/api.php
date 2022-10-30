<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('projects',ProjectController::class)->except(['update','edit'])->middleware('auth:sanctum');
Route::resource('projects.subjects',SubjectController::class)->except(['update','edit','destroy'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/subjects/{subject}')->group(function (){
        Route::get('/proposals',[ProposalController::class,'index']);
    });
});

Route::prefix('/subjects/{subject}')->group(function (){
    Route::get('/proposals',[ProposalController::class,'create']);
    Route::post('/proposals',[ProposalController::class,'store']);
    
});

Route::post('enter-project',[ProjectController::class,'enter']);

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);