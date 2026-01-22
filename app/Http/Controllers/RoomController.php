<?php

namespace App\Http\Controllers;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store (Request $request){
        $validator = Validator::make($request->all(),[
            "name" => "required|string|max:100",
            "description" => "required|string|min:10",
            "type" => "required|in:1,2,3",
            "price" => "required|numeric|min:0",
            "image" => "sometimes|file|mimes:jpg,jpeg,png|max:2048"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Create Room request didn't pass the validation",
            ],400);
        }

        $validated = $validator->validated();
        if(isset($validated['image'])){
            $image = $request->file('image');
        }
        $room = Room::create([
            "name" => $validated["name"],
            "description" => $validated["description"],
            "type" => $validated["type"],
            "price" => $validated["price"],
            "extension" => isset($image) ? $image->getClientOriginalExtension() : null
        ]);
        if(isset($validated['image'])){
            $image->move(public_path('storage/uploads/rooms'), '.'.$image->getClientOriginalExtension());
        }
        return response()->json([
            "ok" => true,
            "message" => "Room has been created successfully",
            "data" => $room
        ],201);
    }

    public function index(){
        return response()->json([
            "ok" => true,
            "message" => "All rooms have been retrieved successfully",
            "data" => Room::paginate(10)
        ],200);
    }

    public function show (Room $room){
        return response()->json([
            "ok" => true,
            "message" => "Room data has been retrieved successfully",
            "data" => $room
        ],200);
    }
}
