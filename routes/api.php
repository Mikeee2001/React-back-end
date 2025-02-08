<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

//admin route in the admin it will check the authentication of your and admin
Route::middleware(['auth:sanctum', 'isAPIAdmin'])->group(function() {

    Route::get('/checkingAuthenticated', function() {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);

    });
 
});

// //admin route in the admin it will check the authentication of your and admin
// Route::middleware(['auth:sanctum', 'isAPIDoctor'])->group(function() {

//     Route::get('/checkingAuthenticated', function() {
//         return response()->json(['message' => 'Welcome Doctor', 'status' => 200], 200);

//     });
 
// });


//user route
Route::middleware(['auth:sanctum'])->group(function() {
    

    Route::post('/logout', [AuthController::class, "logout"]);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
