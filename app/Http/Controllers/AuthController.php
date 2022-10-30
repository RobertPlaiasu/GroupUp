<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Throwable;

class AuthController extends Controller
{

   public function register(Request $request)
   {
        try
        {
            $validate = Validator::make($request->all(),[
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|max:255|string|email|unique:users,email',
                'password' => ['required','confirmed','max:255',Password::min(8)->numbers()->mixedCase()->symbols()],
                'password_confirmation' => 'required'
            ]);

            if($validate->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors(), 
                ],401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
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

   public function login(Request $request)
   {
        try
        {
            $validate = Validator::make($request->all(),[
                
                'email' => 'required|email',
                'password' => ['required'],
            ]);

            if($validate->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors(), 
                ],401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        }
        catch(Throwable $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
   }

}
