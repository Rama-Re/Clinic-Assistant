<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationControllers\CityController;


Route::post("/register", [AuthController::class,'register']);
Route::post("/verify", [AuthController::class,'verify']);
Route::get("/cities", [CityController::class,'index']);

Route::group(['middleware' => ['verifyUser']], function () {
    Route::get("/login", [AuthController::class,'login']);
    Route::post("/editPassword", [AuthController::class,'editPassword']);
}
);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post("/logout", [AuthController::class,'logout']);
}

);

