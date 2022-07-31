<?php

namespace App\Http\Controllers\PatientControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientModels\Disease;
class DiseaseController extends Controller
{
    private static $specialtyarray = array(
        1 =>array('disease_name'=>'ربو حساسية'),
        2 =>array('disease_name'=>'حساسية دوائية'),
        3 =>array('disease_name'=>'قلب'),
        4 =>array('disease_name'=>'ضغط'),
        5 =>array('disease_name'=>'سكري'),
    );

    public static function get_all(){
        return self::$specialtyarray; 
    }

    public static function index(){
        $response = [
            'diseases' => Disease::get(['disease_id','disease_name']),
            'message' => 'Success'
        ];
        return response($response,201);
    }

    public static function getIndex($disease_name){
        if (!Disease::where('disease_name',$disease_name)->first()) {
            $disease = new Disease;
            $disease->disease_name = $disease_name;
            $disease->save();
            return $disease;
        }
        else {
            return Disease::where('disease_name',$disease_name)->get('disease_id')->first();
        }
    }

    public static function save()
    {
        if (empty(Disease::count())) {
            $diseases = self::get_all();

            foreach ($diseases as $key => $value) {
                $disease = [
                    'disease_id' => $key,
                    'disease_name' => $value['disease_name'],
                ];
                Disease::insert($disease);
            }
            $response = [
                'message' => 'diseases added successfully'
            ];
            return response($response,201);
        } else {
            $response = [
                'diseases' => Disease::get(),
                'message' => 'diseases already added'
            ];
            return response($response,201);
        }
    }
}
