<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_as' => 0,// Default role for registered users
            ]);
        }
        $token = $user->createToken($user->email . '_Token')->plainTextToken;

        return response()->json([
            'status' => 200,
            'username' => $user->name,
            'role' => $user->role_as,
            'token' => $token,
            'message' => 'User registered successfully',
        ]);
    }



    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:50',
            'password' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid Credentials',
                ]);


            } 
            else

             {

                if ($user->role_as == 1) //2 is equal to admin
                {
                    $role = 'admin';
                    $token = $user->createToken($user->email.'_AdminToken', ['server:admin'])->plainTextToken;
                }
                elseif($user->role_as == 2) //1 is for doctor
                {
                    $role = 'doctor';
                    $token = $user->createToken($user->email.'_DoctorToken', ['server:doctor'])->plainTextToken;
                }
                 else 
                 {
                    $role = 'user'; //default role for user
                    $token = $user->createToken($user->email . '_Token', [''])->plainTextToken;
                }

                return response()->json([
                    'status' => 200,
                    'username' => $user->name,
                    'token' => $token,
                    'message' => 'Logged in successfully',
                    'role' => $role,
                ]);
            }
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully',
        ]);
    }

}