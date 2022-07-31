<?php

namespace App\Http\Controllers\SharedControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PatientControllers\PatientController;
use App\Http\Controllers\PatientControllers\PatientHealthInfoController;
use App\Models\SharedModels\BookedAppointment;
use App\Models\SharedModels\PatientRecord;
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
        $appointment = BookedAppointment::where('appointment_id',$request->appointment_id)
        //->whereDate('booked_appointments.appointment_date', '=', date('Y-m-d'))
        ->get(['appointment_id','patient_id','dentist_id','appointment_date','duration','Done'])->first();
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
        $dentist_id = DentistController::getDentistByToken($request)->dentist_id;
        $appointments = BookedAppointment::where('dentist_id',$dentist_id)
        ->where('appointment_date','>=',Carbon::createFromFormat('Y-m-d', $request->date))
        ->where('appointment_date','<',Carbon::createFromFormat('Y-m-d', $request->date)->addDay(1))
        //->where('appointment_date','>=',Carbon::createFromFormat('Y-m-d H:i', '2022-07-31 08:00'))
        ->where('Done',False)->get(['appointment_id','patient_id','appointment_date','duration']);
        //return $appointments;
        $response = [
            'appointments' => $appointments,
            'message' => 'success'
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
        $dentist_id = DentistController::getDentistByToken($request)->dentist_id;
        //$now = Carbon::now();
        $now = date('Y-m-d H:i:s');
        //return $now;
        //$now->setTimezone('Asia/Damascus');
        //return $now;
        $appointments = BookedAppointment::where('dentist_id',$dentist_id)
        ->where('appointment_date','<',$now)
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
