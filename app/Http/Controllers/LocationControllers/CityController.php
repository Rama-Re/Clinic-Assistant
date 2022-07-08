<?php

namespace App\Http\Controllers\LocationControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $generalTrait = new GeneralTrait;
        return $generalTrait->returnData("cities",City::get());
    }

    /*
    public static function get_cities_names(){
        return array_column(self::$cityArray, 'name');
    }
    */

    public static function save()
    {
        $generalTrait = new GeneralTrait;

        if (empty(DB::table('cities')->count())) {
            $cities = self::get_all();

            foreach ($cities as $key => $value) {
                $citiesarray = [
                    'city_id' => $key,
                    'city_name' => $value['name'],
                ];
                DB::table('cities')->insert($citiesarray);
            }
            return $generalTrait->returnSuccessMessage('cities added successfully');
        } else {
            return $generalTrait->returnData('cities',DB::table('cities')->get(),'cities already added');
        }
    }
}
