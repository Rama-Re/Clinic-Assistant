<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationControllers\CityController;
use App\Http\Controllers\DentistControllers\DentistController;
use App\Http\Controllers\DentistControllers\SpecialtyController;
use App\Http\Controllers\DentistControllers\MedicalServiceController;


Route::get("/cities", [CityController::class,'index']);
Route::get("/specialties", [SpecialtyController::class,'index']);
Route::get("/medical_services", [MedicalServiceController::class,'index']);


Route::post("/register", [AuthController::class,'register']);
Route::post("/verify", [AuthController::class,'verify']);

Route::group(['middleware' => ['verifyUser']], function () {
    Route::get("/login", [AuthController::class,'login']);
    Route::post("/editPassword", [AuthController::class,'editPassword']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post("/logout", [AuthController::class,'logout']);
    Route::post("/addPrperties", [DentistController::class,'addPrperties']);
    Route::post("/editMainPrperties", [DentistController::class,'editMainPrperties']);
    Route::post("/editSchedule", [DentistController::class,'editSchedule']);
    Route::get("/getProfile", [AuthController::class,'getProfile']);
    Route::get("/getSchedule", [DentistController::class,'getSchedule']);
    // for test
    Route::get("/getServices", [SpecialtyController::class,'getServices']);

});

