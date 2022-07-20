<?php

namespace App\Http\Controllers\LocationControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LocationModels\City;


class CityController extends Controller
{
    private static $cityArray = array(
        1 =>array('name'=>'Aleppo'),
        2 =>array('name'=>'Al-Hasakah'),
        3 =>array('name'=>'Al-Qamishli'),
        4 =>array('name'=>'Al-Qunaytirah'),
        5 =>array('name'=>'Al-Raqqah'),
        6 =>array('name'=>'Al-Suwayda'),
        7 =>array('name'=>'Damascus'),
        8 =>array('name'=>'Daraa'),
        9 =>array('name'=>'Dayr al-Zawr'),
        10 =>array('name'=>'Hamah'),
        11 =>array('name'=>'Homs'),
        12 =>array('name'=>'Idlib'),
        13 =>array('name'=>'Latakia'),
        14 =>array('name'=>'Rif Dimashq'),
    ); 
    
    public static function get_all(){
        return self::$cityArray; 
    }

    public static function index(){
        $response = [
            'cities' => City::get(),
            'message' => 'Success'
        ];
        return response($response,201);
    }

    
    /*
    public static function get_cities_names(){
        return array_column(self::$cityArray, 'name');
    }
    */

    public static function save()
    {
        $generalTrait = new GeneralTrait;

        if (empty(City::count())) {
            $cities = self::get_all();

            foreach ($cities as $key => $value) {
                $citiesarray = [
                    'city_id' => $key,
                    'city_name' => $value['name'],
                ];
                City::insert($citiesarray);
            }
            $response = [
                'message' => 'cities added successfully'
            ];
            return response($response,201);
        } else {
            $response = [
                'cities' => City::get(),
                'message' => 'cities already added'
            ];
            return response($response,201);
            //return $generalTrait->returnData('cities',DB::table('cities')->get(),'cities already added');
        }
    }
}
