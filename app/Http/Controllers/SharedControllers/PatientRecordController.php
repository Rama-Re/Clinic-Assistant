<?php

namespace App\Http\Controllers\SharedControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SharedModels\PatientRecord;
class PatientRecordController extends Controller
{
    public static function record($appointment,$notes) {
        $record = new PatientRecord;
        $record->dentist_id = $appointment->dentist_id;
        $record->patient_id = $appointment->patient_id;
        $record->appointment_id = $appointment->appointment_id;
        $record->notes = $notes;
        $record->save();

        return $record;
    }
    //test
    public static function getPatientRecord($patient_id,$dentist_id) {
        $records = PatientRecord::join('booked_appointments', 'booked_appointments.appointment_id', '=', 'patient_records.appointment_id')
        ->where('patient_records.dentist_id',$dentist_id)
        ->where('patient_records.patient_id',$patient_id)
        //->whereDate('booked_appointments.appointment_date', '=', date('Y-m-d'))
        ->get(['booked_appointments.appointment_date','notes']);
        return $records;
        /*
        $recordArray = array();
        $count = 0;
        foreach ($records as $record) {
            $date = date($record->appointment_date);
            $appointment = [
                'appointment_date' => $date,
                'notes' => $record->notes
            ];
            $recordArray[$count] = $appointment;
            //$recordArray[$count] = Carbon::createFromFormat('Y-m-d H:i:s', $record->appointment_date)->format('Y-m-d H:i:s');
            $count++;
        }
        return $recordArray;
        */
    }
}
