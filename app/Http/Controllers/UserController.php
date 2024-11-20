<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRules;

class UserController extends Controller
{
    //

    public function register(Request $request)
    {

        //validate input request
        $validate = $request->validate([
            'name' => 'required|string|min:6|max:30',
            'email' => 'required|email|string|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRules::min(6)->letters()->numbers(),
            ]
        ]);


        // creat user
        $user = User::create($validate);

        //create access token
        $token = $user->createToken($request->name);

        return ['user' => $user, 'token' => $token->plainTextToken];
    }




    public function login(Request $request)
    {

        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->with('organisation')->first(); // Eager load the organisation

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ['message' => 'In correct credentials'];
        }

        $token = $user->createToken($user->name);

        return ['user' => $user, 'token' => $token->plainTextToken];
    }
}
