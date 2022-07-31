<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationControllers\CityController;
use App\Http\Controllers\DentistControllers\DentistController;
use App\Http\Controllers\PatientControllers\PatientController;
use App\Http\Controllers\DentistControllers\SpecialtyController;
use App\Http\Controllers\DentistControllers\MedicalServiceController;
use App\Http\Controllers\DentistControllers\DentistSpecialtyController;
use App\Http\Controllers\SharedControllers\BookedAppointmentController;
// without auth
Route::get("/cities", [CityController::class,'index']);
Route::get("/specialties", [SpecialtyController::class,'index']);
Route::get("/medical_services", [MedicalServiceController::class,'index']);

Route::post("/register", [AuthController::class,'register']);
Route::post("/verify", [AuthController::class,'verify']);

// need verification
Route::group(['middleware' => ['verifyUser']], function () {
    Route::post("/login", [AuthController::class,'login']);
    Route::post("/editPassword", [AuthController::class,'editPassword']);
});

//general
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post("/logout", [AuthController::class,'logout']);
    Route::get("/getProfile", [AuthController::class,'getProfile']);
});

//dentist
Route::group(['middleware' => ['auth:sanctum','userType:Dentist']], function () {
    Route::post("/addPrperties", [DentistController::class,'addPrperties']);
    Route::post("/dentist/editMainProperties", [DentistController::class,'editMainProperties']);
    Route::post("/editSchedule", [DentistController::class,'editSchedule']);
    Route::get("/getSchedule", [DentistController::class,'getSchedule']);
    Route::get("/getServices", [DentistSpecialtyController::class,'getServices']);
    Route::get("/getNextAppointment", [BookedAppointmentController::class,'getNextAppointment']);
    Route::get("/getPrevAppointment", [BookedAppointmentController::class,'getPrevAppointment']);
    Route::post("/setAppointmentSuccess", [BookedAppointmentController::class,'setAppointmentSuccess']);

});

//patient
Route::group(['middleware' => ['auth:sanctum','userType:Patient']], function () {
    Route::post("/patient/editMainProperties", [PatientController::class,'editMainProperties']);
    Route::post("/canBook", [BookedAppointmentController::class,'canBook']);
    Route::post("/addAppointment", [BookedAppointmentController::class,'addAppointment']);
    Route::post("/editAppointment", [BookedAppointmentController::class,'editAppointment']);
    Route::delete("/deleteAppointment", [BookedAppointmentController::class,'deleteAppointment']);
});

