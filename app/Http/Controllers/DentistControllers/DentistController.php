<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use App\Models\DentistModels\Dentist;
use Illuminate\Http\Request;

class DentistController extends Controller
{
    public static function validateReq(Request $request)
    {
        $result = $request->validate([
            'location' => 'required|string',
            'city_id' => 'required|exists:cities,city_id',
            'work_starting_date' => 'nullable|date'
        ]);

        return $result;

    }
    public static function save($result,$user_id)
    {
        $dentist = new Dentist;
        $dentist->user_id = $user_id;
        $dentist->location = $result['location'];
        $dentist->city_id = $result['city_id'];
        $dentist->work_starting_date = $result['work_starting_date'];
        $dentist->save();

        return $dentist;
    }
    public static function get($user_id) {
        $dentist = Dentist::where('user_id',$user_id)->first();
        return $dentist;
    }

}
