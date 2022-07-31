<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MyValidator extends Controller
{
    public static function make($data, $rules){
        try {
            $validator = Validator::make($data, $rules);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                return [
                    'status' => false,
                    'message' => $validator->errors()->first()
                ];
            }
            else return [
                'status' => true,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
