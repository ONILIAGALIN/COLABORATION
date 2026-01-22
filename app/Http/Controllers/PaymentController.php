<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
class PaymentController extends Controller
{
    public function rent (Request $request){
        $validator = Validator::make($request->all(), [
            "start_date" => "required|date",
            "number_of_months" => "required|integer|min:1|max:999",
            "room_id" => "required|exists:rooms,id"
        ]);
        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "message" => "Request didn't pass the validation.",
                "error" => $validator->errors()
            ],400);
        }

        $validated = $validator->validated();
        $room = Room::find($validated["room_id"]);
        $payment = Payment::create([
            "leased_until" => (new Carbon($validated['start_date']))->addMonths((int)$validated['number_of_months']),
            "user_id" => $request->user()->id,
            "room_id" => $validated["room_id"],
            "status" => "Pending",
            "amount" => (int)$validated["number_of_months"] * $room->price,
        ]);
        return response()->json([
            "ok" => true,
            "message" => "Payment for renting room has been created successfully.",
            "data" => $payment
        ],201);
    }
}
