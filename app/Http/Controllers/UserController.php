<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    public function store (Request $request) {
        $validator = Validator::make($request->all(), [
            "username" => "required|string|min:8|max:100|unique:users",
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
                "message" => "Request didn't pass the Validation",
                "error" => $validator->errors()
            ],400);
        }
        $user_input = $validator->safe()->only(["username","email","password"]);
        $user_input['password'] = Hash::make($user_input['password']);
        $user_input['role_id'] = "User";

        $profile_input = $validator->safe()->except(["username","email","password"]);

        $user = User::create($user_input);
        $user->profile()->create($profile_input);
        $user->profile;

        return response()->json([
            "ok" => true,
            "message" => "Registered successfully",
            "data" => $user->load("profile")
        ],201);
    }

    public function index (){
        return response()->json([
            "ok" => true,
            "message" => "All data form user table has been retrieved.",
            "data" => User::all()->load("profile")
        ],200);
    }

    public function show (User $user){
        return response()->json ([
            "ok" => true,
            "message" => "User data has been retrieved successfully.",
            "data" => $user->load("profile")
        ],200);
    }

    public function update (Request $request, User $user){
        $validator = Validator::make($request->all(), [
            "username" => "sometimes|string|min:8|max:64|unique:users,username," . $user->id,
            "email" => "sometimes|string|email|unique:users,email," . $user->id,
            "role_id" => "sometimes|in:Admin,User",
            "password" => "sometimes|string|min:8|max:255|confirmed",
            "first_name" => "sometimes|string|min:2|max:32",
            "middle_name" => "sometimes|string|min:2|max:32",
            "last_name" => "sometimes|string|min:2|max:32",
            "gender" => "sometimes|in:Male,Female,N/A"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request to Update User unsuccessfully.",
                "error" => $validator->errors()
            ],400);
        }
        $user_input = $validator->safe()->only(["username","email","password","role_id"]);
        
        if(isset($user_input['password']) && $user_input['password']){
            $user_input['password'] = Hash::make($user_input['password']);
        } else {
            unset($user_input['password']);
        }

        $profile_input = $validator->safe()->except(["username","email","password","role_id"]);

        $user->update($user_input);
        $user->profile()->update($profile_input);

        return response()->json([
            "ok" => true,
            "message" => "User updated successfully",
            "data" => $user->load("profile")
        ],200);
    }

    public function destroy (User $user){
        $user->delete();
        return response()->json([
            "ok" => true,
            "message" => "User deleted successfully"
        ],200);
    }
}
