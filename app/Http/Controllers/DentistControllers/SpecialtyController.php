<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use App\Models\DentistModels\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    private static $specialtyarray = array(
        1 =>array('specialty_name'=>'Implant'),
        2 =>array('specialty_name'=>'Surgery'),
        3 =>array('specialty_name'=>'Pediatric'),
        4 =>array('specialty_name'=>'Cosmetic'),
        5 =>array('specialty_name'=>'Orthodontics'),
        6 =>array('specialty_name'=>'Endodontic Treatments'),
        7 =>array('specialty_name'=>'Prosthodontics'),
    );

    public static function get_all(){
        return self::$specialtyarray; 
    }

    public static function index(){
        $response = [
            'specialties' => Specialty::get(),
            'message' => 'Success'
        ];
        return response($response,201);
    }
    public static function save()
    {
        if (empty(Specialty::count())) {
            $specialties = self::get_all();

            foreach ($specialties as $key => $value) {
                $specialty = [
                    'specialty_id' => $key,
                    'specialty_name' => $value['specialty_name'],
                ];
                Specialty::insert($specialty);
            }
            $response = [
                'message' => 'specialties added successfully'
            ];
            return response($response,201);
        } else {
            $response = [
                'specialties' => Specialty::get(),
                'message' => 'specialties already added'
            ];
            return response($response,201);
        }
    }
}
