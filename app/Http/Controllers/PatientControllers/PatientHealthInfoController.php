<?php

namespace App\Http\Controllers\PatientControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientModels\PatientHealthInfo;
use App\Http\Controllers\MyValidator;

class PatientHealthInfoController extends Controller
{
    public static function validateReq(Request $request)
    {
        $result = MyValidator::make($request->only('diseases'), [
                'diseases' => 'required|array',
            ]);

        return $result;
    }

    public static function addHealthInfo(Request $request) {
        $patient = PatientController::getPatientByToken($request);
        $patient_id = $patient->patient_id;
        $result = self::validateReq($request);
        if (!$result['status']) {
            $response = [
                'can' => False,
                'message' => $result['message']
            ];
            return response($response,404);
        }
        $result = $result['data'];
        foreach ($result['diseases'] as $disease) {
            $healthInfo = new PatientHealthInfo;
            $healthInfo->patient_id = $patient_id;
            //return DiseaseController::getIndex($disease);
            $disease_id = DiseaseController::getIndex($disease)->disease_id;
            $healthInfo->disease_id = $disease_id;
            $healthInfo->save();
        }
        $response = [
            'message' => 'added Successfully'
        ];
        return response($response,201);
    }

    public static function getHealthInfo($patient_id) {
        $diseases = PatientHealthInfo::join('diseases', 'diseases.disease_id', '=', 'patient_health_infos.disease_id')
        ->where('patient_health_infos.patient_id',$patient_id)
        ->get('diseases.disease_name');
        $diseaseArray = array();
        $count = 0;
        foreach ($diseases as $disease) {
            $diseaseArray[$count] = $disease->disease_name;
            //$recordArray[$count] = Carbon::createFromFormat('Y-m-d H:i:s', $record->appointment_date)->format('Y-m-d H:i:s');
            $count++;
        }
        return $diseaseArray;
    }

}
