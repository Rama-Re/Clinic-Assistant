<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use App\Models\DentistModels\MedicalService;
use Illuminate\Http\Request;

class MedicalServiceController extends Controller
{
    private static $serviceArray = array(
        1 =>array('service_name'=>'Veneer'),
        2 =>array('service_name'=>'Fillings'),
        3 =>array('service_name'=>'Concsulation'),
        4 =>array('service_name'=>'Preview'),
        5 =>array('service_name'=>'Extraction'),
        6 =>array('service_name'=>'Nerve Extraction'),
        7 =>array('service_name'=>'Remove Braces'),
        8 =>array('service_name'=>'Braces'),
    );

    public static function get_all(){
        return self::$serviceArray; 
    }

    public static function index(){
        $response = [
            'services' => MedicalService::get(['service_id','service_name']),
            'message' => 'Success'
        ];
        return response($response,201);
    }
    public static function save()
    {
        if (empty(MedicalService::count())) {
            $services = self::get_all();

            foreach ($services as $key => $value) {
                $service = [
                    'service_id' => $key,
                    'service_name' => $value['service_name'],
                ];
                MedicalService::insert($service);
            }
            $response = [
                'message' => 'services added successfully'
            ];
            return response($response,201);
        } else {
            $response = [
                'specialties' => MedicalService::get(),
                'message' => 'services already added'
            ];
            return response($response,201);
        }
    }
}
