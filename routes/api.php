<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::post("/register", [AuthController::class,"register"]);
Route::post("/login", [AuthController::class,"login"]);

Route::prefix("users")->group(function () {
    Route::post("/", [App\Http\Controllers\UserController::class, "store"]);
    Route::get("/", [App\Http\Controllers\UserController::class, "index"]);
    Route::get("/{user}", [App\Http\Controllers\UserController::class, "show"]);
    Route::patch("/{user}", [App\Http\Controllers\UserController::class, "update"]);
    Route::delete("/{user}", [App\Http\Controllers\UserController::class, "destroy"]);
});

Route::prefix("rooms")->group(function () {
    Route::post("/", [App\Http\Controllers\RoomController::class, "store"]);
    Route::get("/", [App\Http\Controllers\RoomController::class, "index"]);
    Route::get("/{room}", [App\Http\Controllers\RoomController::class, "show"]);
    Route::patch("/{room}", [App\Http\Controllers\RoomController::class, "update"]);
    Route::delete("/{room}", [App\Http\Controllers\RoomController::class, "destroy"]);
});
Route::prefix("payments")->middleware("auth:sanctum")->group(function () {
    Route::post("/", [App\Http\Controllers\PaymentController::class, "rent"]);
    Route::get("/", [App\Http\Controllers\PaymentController::class, "index"]);
    Route::get("/{payment}", [App\Http\Controllers\PaymentController::class, "show"]);
    Route::patch("/{payment}", [App\Http\Controllers\PaymentController::class, "update"]);
    Route::delete("/{payment}", [App\Http\Controllers\PaymentController::class, "destroy"]);
});