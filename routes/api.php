<?php

use App\Http\Controllers\GuestController;
use App\Http\Controllers\UserController;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//auth
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/', function () {
    return 'Hello';
});

//password reset
Route::post('password/email', [UserController::class, 'sendResetLinkEmail'])->middleware('guest');
Route::post('password/verify', [UserController::class, 'verifyCode'])->middleware('guest');
Route::post('password/reset', [UserController::class, 'resetPassword'])->middleware('guest');

//guests
Route::post('/guests', [GuestController::class, 'index']);
Route::post('/guests/store', [GuestController::class, 'store']);
Route::post('/guests/search', [GuestController::class, 'search']);
Route::put('/guests/update/{id}', [GuestController::class, 'update']);


Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
