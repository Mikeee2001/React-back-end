<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        $users = User::all(['id', 'name', 'email']);
        return response()->json(['users' => $users]);
    }

    // Get a single user
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    // Create a new user
    public function store(Request $request)
    {
       
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'auth_role' => 'required|string|max:255'
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password
                'auth_role' => $request->auth_role
            ]);
    
            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        }


    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->update($request->all());
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    // Appoint a user as doctor
    // public function appointDoctor($id)
    // {
    //     $user = User::find($id);
    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }
    //     $user->is_doctor = true;
    //     $user->save();
    //     return response()->json(['message' => 'User appointed as doctor successfully']);
    // }
}
