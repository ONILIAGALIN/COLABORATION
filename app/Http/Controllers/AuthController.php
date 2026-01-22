<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register (Request $request){
        $validator = Validator::make($request->all(), [
            "username" => "required|string|min:8|max:64|unique:users",
            "email" => "required|string|email|unique:users",
            "password" => "required|string|min:8|max:255|confirmed",
            "first_name" => "required|string|min:2|max:32",
            "middle_name" => "sometimes|string|min:2|max:32",
            "last_name" => "required|string|min:2|max:32",
            "gender" => "required|in:Male,Female,N/A"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request didn't pass the validation",
                "error" => $validator->errors()
            ],400);
        }

        $user_input = $validator->safe()->only(["username","email","password","role_id"]);
        $profile_input = $validator->safe()->except(["username","email","password","role_id"]);
        $user = User::create($user_input);
        $user->profile()->create($profile_input);
        $user->profile;
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            "ok" => true,
            "message" => "Registered Succefully",
            "token" => $token,
            "data" => $user->load("profile")
        ],201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            "username" => "required_without:email|string",
            "email" => "required_without:username|string",
            "password" => "required|string",
        ]);

        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request didn't pass the validation",
                "error" => $validator->errors()
            ],422);
        }

        $credentials = $validator->validated();
        $fieldType = isset($credentials['email']) ? 'email' : 'username';
        $login = $credentials[$fieldType] ?? null;
        $user = User::all()->first(function ($user) use ($fieldType, $login) {
            return $user->$fieldType === $login;
        });
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                "ok" => false,
                "message" => "Please check your Username or Email and Password!"
            ], 401);
        }

        $user->profile;
        $user->token = $user->createToken("login_token")->plainTextToken;

        return response()->json([
            "ok" => true,
            "message" => "Login Successfully.",
            "data" => $user
        ], 200);
    }

}
