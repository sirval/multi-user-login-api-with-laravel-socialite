<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

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

    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function gotoProvider($provider)
    {
        $validated = $this->validateProvider($provider);
        if ($validated) {
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * get user information from provider
     *
     * @param $provider
     * @return JsonResponse
     */

     public function providerCallback($provider)
     {
         $validated = $this->validateProvider($provider);
         if (!is_null($validated)) {
             return $validated;
         }
         try {
             $user = Socialite::driver($provider)->stateless()->user();
         } catch (ClientException $exception) {
             return response()->json(['error'=>"Invalid credentials provided"], 422);
         }

         $userCreated = User::firstOrCreate([
             'email' => $user->getEmail()
         ],
         [
             'email_verified_at' => now(),
             'name'=>$user->getName(),
             'status'=>true,
         ]
         );

         $userCreated->providers()->updateOrCreate(
             [
                 'provider' => $provider,
                 'provider_id' => $user->getId(),
             ],
             [ 
                 'avatar' => $user->getAvatar
             ]
             );
             $token = $userCreated->createToken('appToken')->plainTextToken;
             return response()->json($userCreated, 200, ['Access-Token'=> $token]);
     }

     
    /**
     * validate provider
     * @param $provider
     * @return JsonResponse
     */

     public function validateProvider($provider)
     {
         if (!in_array($provider, ['facebook', 'github', 'twitter', 'google'])) {
            return response()->json(['error' => 'Please login using facebook, github, twitter or google'], 422);
         }
     }

}
