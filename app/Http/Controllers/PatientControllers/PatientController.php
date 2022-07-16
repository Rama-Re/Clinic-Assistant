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
            'bearth_day' => 'required|date'
        ]);

        return $result;

    }
    public static function save($result,$user_id)
    {
        $patient = new Patient;
        $patient->user_id = $user_id;
        $patient->location = $result['location'];
        $patient->city_id = $result['city_id'];
        $patient->bearth_day = $result['bearth_day'];
        $patient->save();

        return $patient;
    }
    public static function get($user_id) {
        $patient = Patient::where('user_id',$user_id)->first();
        return $patient;
    }
}
