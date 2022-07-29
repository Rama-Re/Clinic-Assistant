<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DentistModels\SpecialtyService;

class SpecialtyServiceController extends Controller
{
    private static $serviceArray = array(
        1 =>array('specialty_id'=>3, 'service_id'=>2),
        2 =>array('specialty_id'=>3, 'service_id'=>5),
        3 =>array('specialty_id'=>3, 'service_id'=>6),
        4 =>array('specialty_id'=>6, 'service_id'=>6),
        5 =>array('specialty_id'=>4, 'service_id'=>2),
        6 =>array('specialty_id'=>4, 'service_id'=>1),
        7 =>array('specialty_id'=>5, 'service_id'=>7),
        8 =>array('specialty_id'=>5, 'service_id'=>8),
    );

    public static function get_all(){
        return self::$serviceArray; 
    }

    public static function save()
    {
        if (empty(SpecialtyService::count())) {
            $services = self::get_all();

            foreach ($services as $key => $value) {
                $service = [
                    'sservice_id' => $key,
                    'specialty_id' => $value['specialty_id'],
                    'service_id' => $value['service_id'],
                ];
                SpecialtyService::insert($service);
            }
            $response = [
                'message' => 'services added successfully'
            ];
            return response($response,201);
        } else {
            $response = [
                'specialties' => SpecialtyService::get(),
                'message' => 'services already added'
            ];
            return response($response,201);
        }
    }
}
