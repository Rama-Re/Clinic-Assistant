<?php

namespace App\Http\Controllers\PatientControllers;

use App\Http\Controllers\Controller;
use App\Models\PatientModels\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public static function validateReq(Request $request)
    {
        $result = $request->validate([
            'location' => 'required|string',
            'city_id' => 'required|exists:cities,city_id',
            'bearth_date' => 'required|date'
        ]);

        return $result;

    }
    public static function save($result,$user_id)
    {
        $patient = new Patient;
        $patient->user_id = $user_id;
        $patient->location = $result['location'];
        $patient->city_id = $result['city_id'];
        $patient->bearth_date = $result['bearth_date'];
        $patient->save();

        return $patient;
    }
    public static function get($user_id) {
        $patient = Patient::where('user_id',$user_id)->first();
        return $patient;
    }
    public static function getProfile($user_id)
    {
        $patient = PatientController::get($user_id);
        $patient = $dentist->dentist_id;
        $profile = [
            'location' => $dentist->location,
            'city_id' => $dentist->city_id,
            'bearth_date' => $dentist->bearth_date,
        ];

        return $profile;
    }

    public static function editMainProperties(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;
        $result = $request->validate([
            'location' => 'required|string',
            'city_id' => 'required|exists:cities,city_id',
        ]);
        $patient = PatientController::get($user_id);
        $patient_id = $patient->patient_id;
        $patient->location = $result['location'];
        $patient->city_id = $result['city_id'];
        $patient->save();
        if ($patient) {
            $response = [
                'message' => 'properties edited succesfully'
            ];
            return response($response,201);
        }
        $response = [
            'message' => 'something went wrong through editing prperties'
        ];
        return response($response,401);
    }
}
