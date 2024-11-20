<?php

use App\Http\Controllers\GuestController;
use App\Http\Controllers\UserController;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', function () {

    $users = User::with('organisation')->get();

    return response()->json($users);
});


//guests
Route::post('/guests', [GuestController::class, 'index']);
Route::post('/guests/store', [GuestController::class, 'store']);


Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
