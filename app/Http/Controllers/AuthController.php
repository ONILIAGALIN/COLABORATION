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
            ]);
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
            "token" => $token
        ]);
    }

    public function login(Request $request){
        $validator = Validator($request->all(), [
            "login" => "required|string",
            "password" => "required|string"
        ]);

        if($validator->fails()) {
            return response()->json([
                "ok" => false,
                "message" => "Validation failed",
                "errors" => $validator->errors()
            ], 422);
        }

    $credentials = $validator->validated();
    $fieldType = filter_var($credentials["login"], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    $user = User::where($fieldType, $credentials["login"])->first();
        if (!$user || !Hash::check($credentials["password"], $user->password)) {
            return response()->json([
                "ok" => false,
                "message" => "Invalid Username or Password"
            ], 401);
        }

        $user->load('profile');
        $user->token = $user->createToken("login_token")->plainTextToken;

        return response()->json([
            "ok" => true,
            "message" => "Login Successful",
            "data" => $user
        ]);
    }
}
