<?php

namespace App\Http\Controllers\SharedControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PatientControllers\PatientController;
use App\Http\Controllers\PatientControllers\PatientHealthInfoController;
use App\Models\SharedModels\BookedAppointment;
use App\Models\SharedModels\PatientRecord;
use App\Models\PatientModels\Patient;
use App\Models\User;
use App\Http\Controllers\DentistControllers\ScheduleController;
use App\Http\Controllers\DentistControllers\DentistController;
use App\Http\Controllers\MyValidator;
use Illuminate\Support\Facades\DB;
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

    //test
    public static function getAppointmentInfo(Request $request) {
        $appointment = User::
        join('patients','patients.user_id','=','users.id')
        ->join('booked_appointments','booked_appointments.patient_id','=','patients.patient_id')
        ->where('booked_appointments.appointment_id',$request->appointment_id)
        ->get(['booked_appointments.appointment_id','booked_appointments.patient_id','users.name','booked_appointments.dentist_id','booked_appointments.appointment_date','booked_appointments.duration','booked_appointments.Done'])->first();
        $records = PatientRecordController::getPatientRecord($appointment->patient_id,$appointment->dentist_id);
        $healthInfo = PatientHealthInfoController::getHealthInfo($appointment->patient_id);
        $response = [
            'appointment' => $appointment,
            'records' => $records,
            'healthInfo' => $healthInfo,
        ];
        return response($response,201);
    }

    public static function checkForAnotherAppointment(Request $request) {
        $patient_id = PatientController::getPatientByToken($request)->patient_id;
        $appointment = BookedAppointmentController::isExist($request,$patient_id);
        if (!$appointment) {
            $response = [
                'is_exist' => False,
                'message' => 'this patient doesn\'t have any appointment'
            ];
            return response($response,201);
        }
        $response = [
            'is_exist' => True,
            'appointment' => $appointment,
            'message' => 'there is another appointment for this patient'
        ];
        return response($response,201);
    }
    //delete commint
    public static function addAppointment(Request $request) {
        $result = BookedAppointmentController::validateReq($request);
        $patient_id = PatientController::getPatientByToken($request)->patient_id;
        //if (!BookedAppointmentController::isExist($request,$patient_id)) {
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
        //}
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
        $bookedAppointment->timestamps = false;
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

    public static function getAppointmentsAtDate(Request $request) {
        $dentist = DentistController::getDentistByToken($request);
        $dentist_id = $dentist->dentist_id;
        //$name = User::where('type','Patient')->where()
        //$appointments = BookedAppointment::
        $appointments = User::
        join('patients','patients.user_id','=','users.id')
        ->join('booked_appointments','booked_appointments.patient_id','=','patients.patient_id')
        ->where('booked_appointments.dentist_id',$dentist_id)
        ->where('booked_appointments.appointment_date','>=',Carbon::createFromFormat('Y-m-d H:s:i', (($request->date).' 00:00:00')))
        ->where('booked_appointments.appointment_date','<',Carbon::createFromFormat('Y-m-d H:s:i', (($request->date).' 00:00:00'))->addDay(1))
        ->where('booked_appointments.Done',False)->orderBy('booked_appointments.appointment_date')
        ->get(['booked_appointments.appointment_id','booked_appointments.patient_id','users.name','booked_appointments.appointment_date','booked_appointments.duration']);
        $response = [
            'appointments' => $appointments,
            'message' => 'success'
        ];
        return response($response,201);
    }

    public static function getNextAppointment(Request $request) {
        $dentist = DentistController::getDentistByToken($request);
        $dentist_id = $dentist->dentist_id;
        $now = Carbon::now()->addHour(3);
        $appointments = User::
        join('patients','patients.user_id','=','users.id')
        ->join('booked_appointments','booked_appointments.patient_id','=','patients.patient_id')
        ->where('booked_appointments.dentist_id',$dentist_id)
        ->where('booked_appointments.appointment_date','>=',$now)
        ->orderBy('booked_appointments.appointment_date')
        ->get(['booked_appointments.appointment_id','booked_appointments.patient_id','users.name','booked_appointments.appointment_date','booked_appointments.duration','booked_appointments.Done']);
        $response = [
            'appointments' => $appointments,
            'message' => 'success'
        ];
        return response($response,201);

    }

    public static function setAppointmentSuccess(Request $request) {
        $dentist_id = DentistController::getDentistByToken($request)->dentist_id;
        $appointment = BookedAppointment::where('dentist_id',$dentist_id)
        //->where('appointment_date', '=', date('Y-m-d'))
        ->where('appointment_id',$request->appointment_id)
        ->get()->first();
        $appointment->timestamps = false;
        //$appointment->Done = True;
        $appointment->update(['Done' => True]);
        //$appointment->save();
        
        //return $appointment_date;
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
        $dentist = DentistController::getDentistByToken($request);
        $dentist_id = $dentist->dentist_id;
        $now = Carbon::now()->addHour(3);
        $appointments = User::
        join('patients','patients.user_id','=','users.id')
        ->join('booked_appointments','booked_appointments.patient_id','=','patients.patient_id')
        ->where('booked_appointments.dentist_id',$dentist_id)
        ->where('booked_appointments.appointment_date','<',$now)
        ->orderBy('booked_appointments.appointment_date')
        ->get(['booked_appointments.appointment_id','booked_appointments.patient_id','users.name','booked_appointments.appointment_date','booked_appointments.duration','booked_appointments.Done']);
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
        $result = MyValidator::make($request->only('dentist_id','appointment_date','duration'), [
        //$result = MyValidator::make(['dentist_id','appointment_date','duration'], [
            'dentist_id'=> 'required|exists:dentists,dentist_id',
            'appointment_date' => 'required|date',
            'duration' => 'required|numeric'
        ]);
        if (!$result['status']) {
            $response = [
                'can' => False,
                'message' => $result['message']
            ];
            return response($response,404);
        }
        $result = $result['data'];
        /*$result = $request->validate([
            'dentist_id'=> 'required|exists:dentists,dentist_id',
            'appointment_date' => 'required|date',
            'duration' => 'required|numeric'
        ]);*/
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
