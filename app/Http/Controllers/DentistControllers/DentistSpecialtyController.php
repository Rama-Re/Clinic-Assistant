<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DentistModels\DentistSpecialty;
class DentistSpecialtyController extends Controller
{
    public static function validateReq(Request $request)
    {
        $result = $request->validate([
            'dentist_specialties' => 'required|array',
            'dentist_specialties.*'=> 'exists:specialties,specialty_id',
        ]);

        return $result;
    }

    public static function getSpecialties($dentist_id)
    {
        $specialties = DentistSpecialty::join('specialties', 'specialties.specialty_id', '=', 'dentist_specialties.specialty_id')
        ->where('dentist_specialties.dentist_id',$dentist_id)
        ->get('specialties.specialty_name');
        $specialtiesArray = array();
        $count = 0;
        foreach ($specialties as $specialty) {
            $specialtiesArray[$count] = $specialty['specialty_name'];
            $count++;
        }
        return $specialtiesArray;
    }

    public static function save($result,$dentist_id)
    {
        $specialties = $result['dentist_specialties'];
        foreach ($specialties as $specialty) {
            $dentist_specialty = new DentistSpecialty;
            $dentist_specialty->specialty_id = $specialty;
            $dentist_specialty->dentist_id = $dentist_id;
            $dentist_specialty->save();
            if (!$dentist_specialty) return False;
        }
        return True; 
    }
    public static function edit($result,$dentist_id)
    {
        if (DentistSpecialty::where('dentist_id',$dentist_id)->delete()) {
            $specialties = $result['dentist_specialties'];
            foreach ($specialties as $specialty) {
                $dentist_specialty = new DentistSpecialty;
                $dentist_specialty->specialty_id = $specialty;
                $dentist_specialty->dentist_id = $dentist_id;
                $dentist_specialty->save();
                if (!$dentist_specialty) return False;
            }
            return True;
        }
        else return False;
    }
}
