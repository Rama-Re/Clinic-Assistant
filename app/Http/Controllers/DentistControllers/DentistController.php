<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use App\Models\DentistModels\Dentist;
use App\Models\DentistModels\Specialty;
use App\Models\User;
use App\Models\LocationModels\City;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\DentistControllers\ScheduleController;
use App\Http\Controllers\DentistControllers\DentistSpecialtyController;
class DentistController extends Controller
{
    public static function validateReq(Request $request){
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

    //test
    public static function getAllBySpecialty(Request $request)
    {
        $name = $request->specialty_name;
        $specialty_id = Specialty::where('specialty_name',$name)->get('specialty_id')->first();
        if (!$specialty_id) {
            $response = [
                'message' => 'this specialty isn\'t exist'
            ];
            return response($response,401);
        }
        $dentists = User::join('dentists','dentists.user_id','=','users.id')
        ->join('dentist_specialties', 'dentist_specialties.dentist_id','=','dentists.dentist_id')
        ->join('cities','cities.city_id','=','dentists.city_id')
        ->where('dentist_specialties.specialty_id',$specialty_id->specialty_id)
        ->get(['dentists.dentist_id','users.name','cities.city_name','dentists.location','dentists.work_starting_date']);
        $response = [
            'dentists' => $dentists
        ];
        return response($response,201);
    }
    

    public static function get($user_id)
    {
        $dentist = Dentist::where('user_id',$user_id)->first();
        return $dentist;
    }

    public static function getProfile($user_id)
    {
        $dentist_id = self::get($user_id)->dentist_id;
        $dentist = City::
        join('dentists','dentists.city_id','=','cities.city_id')
        ->where('dentists.dentist_id',$dentist_id)
        ->get(['cities.city_name','dentists.dentist_id','dentists.location','dentists.work_starting_date'])->first();
        $dentist_specialties = DentistSpecialtyController::getSpecialties($dentist_id);
        $profile = [
            'dentist_id' => $dentist->dentist_id,
            'location' => $dentist->location,
            'city_name' => $dentist->city_name,
            'work_starting_date' => $dentist->work_starting_date,
            'dentist_specialties' => $dentist_specialties
        ];

        return $profile;
    }

    public static function getProfileByID(Request $request)
    {
        $dentist = City::
        join('dentists','dentists.city_id','=','cities.city_id')
        ->where('dentists.dentist_id',$request->dentist_id)
        ->get(['cities.city_name','dentists.dentist_id','dentists.location','dentists.work_starting_date'])->first();
        $dentist_specialties = DentistSpecialtyController::getSpecialties($request->dentist_id);
        $user = User::
        join('dentists','dentists.user_id','=','users.id')
        ->where('dentists.dentist_id',$request->dentist_id)
        ->get(['users.name','users.phone_number'])->first();
        $profile = [
            //'dentist_id' => $dentist->dentist_id,
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'location' => $dentist->location,
            'city_name' => $dentist->city_name,
            'work_starting_date' => $dentist->work_starting_date,
            'dentist_specialties' => $dentist_specialties
        ];

        return $profile;
    }
    
    public static function getSchedule(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;
        $dentist = DentistController::get($user_id);
        $dentist_id = $dentist->dentist_id;
        $schedule = ScheduleController::getSchedule($dentist_id);
        
        if ($schedule){
            $response = [
                'schedule' => $schedule
            ];
            return response($response,201);
        }
        $response = [
            'message' => 'something went wrong through gitting schedule'
        ];
        return response($response,401);
    }

    public static function getDentistByToken(Request $request) {
        $user = auth()->user();
        $user_id = $user->id;
        $dentist = DentistController::get($user_id);
        return $dentist;
    }

    public static function addPrperties(Request $request)
    {
        $dentist_id = $request->dentist_id;
        $result = DentistSpecialtyController::validateReq($request);
        $result2 = ScheduleController::validateReq($request);
        if (DentistSpecialtyController::save($result,$dentist_id)) {
            $schedule = ScheduleController::save($result2,$dentist_id);
            if ($schedule){
                $response = [
                    'message' => 'properties added succesfully'
                ];
                return response($response,201);
            }
            $response = [
                'message' => 'something went wrong through adding schedule'
            ];
            return response($response,401);
        }
        $response = [
            'message' => 'something went wrong through adding specialties'
        ];
        return response($response,401);
    }

    public static function editMainProperties(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;
        $result = $request->validate([
            'location' => 'required|string',
            'city_id' => 'required|exists:cities,city_id',
        ]);
        $result2 = DentistSpecialtyController::validateReq($request);
        $dentist = DentistController::get($user_id);
        $dentist_id = $dentist->dentist_id;
        $dentist->location = $result['location'];
        $dentist->city_id = $result['city_id'];
        $dentist->save();

        if (DentistSpecialtyController::edit($result2,$dentist_id)) {
            $response = [
                'message' => 'properties edited succesfully'
            ];
            return response($response,201);
        }
        $response = [
            'message' => 'something went wrong through editing specialties'
        ];
        return response($response,401);
    }
    
    public static function editSchedule(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;
        $result2 = ScheduleController::validateReq($request);
        $dentist = DentistController::get($user_id);
        $dentist_id = $dentist->dentist_id;
        if (ScheduleController::edit($result2,$dentist_id)) {
            $response = [
                'message' => 'schedule edited succesfully'
            ];
            return response($response,201);
        }
        $response = [
            'message' => 'something went wrong through editing schedule'
        ];
        return response($response,401);
    }
    

}
