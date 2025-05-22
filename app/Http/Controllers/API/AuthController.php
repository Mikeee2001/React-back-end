<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        }

        // Store validated request data & ensure role is a string
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_as' => 'user', // Default role must be a STRING
        ]);

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
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Credentials',
            ]);
        }

        // Debugging: Log user's role before checking access
        Log::info("User Role Retrieved: " . $user->role_as);

        // Ensure role_as matches ENUM values
        switch (strtolower($user->role_as)) {
            case 'admin':
                $role = 'admin';
                $token = $user->createToken($user->email . '_AdminToken', ['server:admin'])->plainTextToken;
                break;
            case 'doctor':
                $role = 'doctor';
                $token = $user->createToken($user->email . '_DoctorToken', ['server:doctor'])->plainTextToken;
                break;
            default:
                $role = 'user';
                $token = $user->createToken($user->email . '_UserToken')->plainTextToken;
                break;
        }

        return response()->json([
            'status' => 200,
            'username' => $user->name,
            'token' => $token,
            'message' => 'Logged in successfully',
            'role' => $role,
        ]);
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
