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
}
