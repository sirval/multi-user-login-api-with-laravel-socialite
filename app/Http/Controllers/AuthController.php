<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function Register(Request $request)
    {
        //validating inputs
       $fields = $request->validate([
        'name' => 'required|string',
        'email' => 'required|string|unique:users,email',
        'password' => 'required|same:confirm',
       ]);

       //create user
       $user = User::create([
        'name' => $fields['name'],
        'email' => $fields['email'],
        'password' => bcrypt($fields['password']),
       ]);

       // create token 
       $token = $user->createToken('appToken')->plainTextToken;
       //create response
       $response = [
           'user' => $user,
           'token' => $token,
       ];
       // return response
       return response($response, 201);
    }

    public function Login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // check email
        $whereEmail = ['email' => $fields['email']];
        $user = User::where($whereEmail)->first();

        // check password

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return ([
                'message' => "Invalid Login Credentials", 
                'status' => 401,
            ]);
        }
        $token = $user->createToken('appToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];
        return response($response, 201);
    }

    public function Logout(Request $request)
    {
        Auth()->user()->tokens()->delete();

        $response = [
            'message' => "User Logged Out",
        ];

        return response($response, 201);

    }
}
