<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\AuthController;

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

//login and register api
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);


//ADMIN ROUTE
Route::middleware(['auth:sanctum', 'isAPIAdmin'])->group(function() {

    Route::get('/checkingAuthenticated', function() {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);

    });

});

//DOCTOR ROUTE
Route::middleware(['auth:sanctum', 'isAPIDoctor'])->group(function() {
    Route::get('/checkingAuthenticatedDoctor', function() {
        $user = auth()->user();

        if ($user->role_as == 'doctor') { // CHECK ROLE IF DOCTOR AND IT WILL GOTO DOCTOR DASHBOARD
            return response()->json(['message' => 'Welcome Doctor', 'status' => 200], 200);
        } else {
            return response()->json(['message' => 'Access Denied! You are not a Doctor.', 'status' => 403], 403);
        }
    });
});


//logout button
Route::middleware(['auth:sanctum'])->group(function() {


    Route::post('/logout', [AuthController::class, "logout"]);
});


//get the total number of users
Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
    $users = \App\Models\User::all(['name', 'email']);
    return response()->json(['users' => $users]);
});

//Implements the methods for all CRUD operations: index, show, store, update, destroy, and appointDoctor.
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('users', UserController::class);

});
