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
Route::post('/logout', [UserController::class, 'login']);
Route::post('/', function () {
    return 'Hello';
});


//guests
Route::post('/guests', [GuestController::class, 'index']);
Route::post('/guests/store', [GuestController::class, 'store']);


Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
