<?php

namespace App\Http\Controllers\SharedControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PatientControllers\PatientController;
use App\Models\SharedModels\BookedAppointment;
use App\Models\SharedModels\PatientRecord;
use App\Http\Controllers\DentistControllers\ScheduleController;
use App\Http\Controllers\DentistControllers\DentistController;
use Carbon\Carbon;

class BookedAppointmentController extends Controller
{
    public static function validateReq(Request $request)
    {
        $result = $request->validate([
            'dentist_id'=> 'required|exists:dentists,dentist_id',
            'appointment_date' => 'required|date',
            'duration' => 'required|numeric'
        ]);

        return $result;
    }

    public static function isExist(Request $request,$patient_id){
        $result = BookedAppointment::where('dentist_id',$request->dentist_id)
        ->where('patient_id',$patient_id)
        ->where('Done',False)->get()->first();
        return $result;
    }

    public static function addAppointment(Request $request) {
        $result = BookedAppointmentController::validateReq($request);
        $patient_id = PatientController::getPatientByToken($request)->patient_id;
        if (!BookedAppointmentController::isExist($request,$patient_id)) {
            $bookedAppointment = new BookedAppointment;
            $bookedAppointment->dentist_id = $result['dentist_id'];
            $bookedAppointment->patient_id = $patient_id;
            $bookedAppointment->appointment_date = $result['appointment_date'];
            $bookedAppointment->duration = $result['duration'];
            $bookedAppointment->save();
    
            $response = [
                'bookedAppointment' => $bookedAppointment,
                'message' => 'success'

            ];
            return response($response,201);
        }
        $response = [
            'message' => 'you have already booked appointment'
        ];
        return response($response,401);
    }

    public static function editAppointment(Request $request) {
        $result = BookedAppointmentController::validateReq($request);
        $patient_id = PatientController::getPatientByToken($request)->patient_id;
        
        $bookedAppointment = BookedAppointmentController::isExist($request,$patient_id);
        if (!$bookedAppointment) {
            $response = [
                'message' => 'you don\'t have appointment to edit it'
            ];
            return response($response,401);
        }
        $bookedAppointment->patient_id = $patient_id;
        $bookedAppointment->appointment_date = $result['appointment_date'];
        $bookedAppointment->duration = $result['duration'];
        $bookedAppointment->save();

        $response = [
            'bookedAppointment' => $bookedAppointment,
            'message' => 'success'
        ];
        return response($response,201);
    }

    public static function deleteAppointment(Request $request) {
        $result = BookedAppointment::where('dentist_id',$request->dentist_id)
        ->where('patient_id',PatientController::getPatientByToken($request)->patient_id)
        ->where('Done',False)->delete();
        if ($result) {
            $response = [
                'data' => $result,
                'message' => 'success'
            ];
            return response($response,201);
        }
        $response = [
            'message' => 'there is no Appointment to delete'
        ];
        return response($response,201);
    }

    public static function getNextAppointment(Request $request) {
        $dentist_id = DentistController::getDentistByToken($request)->dentist_id;
        $now = Carbon::now();
        $appointments = BookedAppointment::where('dentist_id',$dentist_id)
        ->where('appointment_date','>=',Carbon::now())
        //->where('appointment_date','>=',Carbon::createFromFormat('Y-m-d H:i', '2022-07-31 08:00'))
        ->where('Done',False)->get(['appointment_id','patient_id','appointment_date','duration']);
        $response = [
            'appointments' => $appointments,
            'message' => 'success'
        ];
        return response($response,201);

    }

    public static function setAppointmentSuccess(Request $request) {
        $dentist_id = DentistController::getDentistByToken($request)->dentist_id;
        $appointment = BookedAppointment::where('dentist_id',$dentist_id)
        ->where('appointment_id',$request->appointment_id)->get()->first();
        //->where('appointment_date',$request->appointment_date)->get()->first();
        $appointment->Done = True;
        $appointment->save();
        if ($appointment) {
            $record = PatientRecordController::record($appointment,$request->notes);
            $response = [
                'appointment' => $appointment,
                'message' => 'success'
            ];
            return response($response,201);
        }
        $response = [
            'message' => 'failed'
        ];
        return response($response,401);
    }

    public static function getPrevAppointment(Request $request) {
        $dentist_id = DentistController::getDentistByToken($request)->dentist_id;
        $now = Carbon::now();
        $appointments = BookedAppointment::where('dentist_id',$dentist_id)
        ->where('appointment_date','<',Carbon::now())
        ->get(['appointment_id','patient_id','appointment_date','duration','Done']);
        $response = [
            'appointments' => $appointments,
            'message' => 'success'
        ];
        return response($response,201);

    }

    public static function checkBookedAppointment($result) {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $result['appointment_date']);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $result['appointment_date'])
        ->addMinutes($result['duration']);
        $workTimes = BookedAppointment::where('dentist_id',$result['dentist_id'])
        ->where('Done',False)->get();
        foreach ($workTimes as $time) {

            $startWork = Carbon::createFromFormat('Y-m-d H:i:s', $time['appointment_date']);
            $endWork = Carbon::createFromFormat('Y-m-d H:i:s', $time['appointment_date'])
            ->addMinutes($time['duration']);
            //return $startWork;
            //$endWork = Carbon::createFromFormat('H:i:s', $time['end']);
            if (($start->gte($startWork) & $start->lt($endWork)) | 
                ($end->gte($startWork) & $end->lt($endWork))) {
                return [
                    'can' => False,
                    'message' => 'there is another appointment at this time'
                ];
            }
        }
        return [
            'can' => True,
            'message' => 'success'
        ];
    }

    public static function canBook(Request $request) {
        $result = $request->validate([
            'dentist_id'=> 'required|exists:dentists,dentist_id',
            'appointment_date' => 'required|date',
            'duration' => 'required|numeric'
        ]);
        $patient_id = PatientController::getPatientByToken($request)->patient_id;
        $response = ScheduleController::checkWorkTime($result,$patient_id);
        if ($response['can']) {
            $response2 = BookedAppointmentController::checkBookedAppointment($result,$patient_id);
            if ($response2['can']) {
                $response3 = [
                    'can' => True,
                    'message' => 'success'
                ];
                return response($response,201);
            }
            return response($response2,201);
        }
        return response($response,201);
    }

}
