<?php

namespace App\Http\Controllers\SharedControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PatientControllers\PatientController;
use App\Http\Controllers\PatientControllers\PatientHealthInfoController;
use App\Models\SharedModels\BookedAppointment;
use App\Models\SharedModels\PatientRecord;
use App\Models\PatientModels\Patient;
use App\Models\LocationModels\City;
use App\Models\User;
use App\Models\DentistModels\Dentist;
use App\Http\Controllers\DentistControllers\ScheduleController;
use App\Http\Controllers\DentistControllers\DentistController;
use App\Http\Controllers\MyValidator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookedAppointmentController extends Controller
{
    public static function validateReq(Request $request) {
        $result = $request->validate([
            'dentist_id'=> 'required|exists:dentists,dentist_id',
            'day' => 'numeric',
            'duration' => 'required|numeric'
        ]);

        return $result;
    }

    public static function isExist(Request $request,$patient_id){
        $result = BookedAppointment::
        where('dentist_id',$request->dentist_id)
        ->where('patient_id',$patient_id)
        ->where('appointment_date','>=',Carbon::now()->addHour(3))
        //->where('Done',False)
        ->get();
        return $result;
    }

    public static function getAppointmentInfo(Request $request) {
        $appointment = User::
            join('patients','patients.user_id','=','users.id')
            ->join('booked_appointments','booked_appointments.patient_id','=','patients.patient_id')
            ->join('cities','cities.city_id','=','patients.city_id')
            ->where('booked_appointments.appointment_id',$request->appointment_id)
            ->get(['booked_appointments.appointment_id','booked_appointments.patient_id','users.name','users.phone_number','patients.bearth_date','patients.location','cities.city_name','booked_appointments.dentist_id','booked_appointments.appointment_date','booked_appointments.duration','booked_appointments.Done'])->first();
        $records = PatientRecordController::getPatientRecord($appointment->patient_id,$appointment->dentist_id);
        $healthInfo = PatientHealthInfoController::getHealthInfo($appointment->patient_id);
        $response = [
            'appointment' => $appointment,
            'records' => $records,
            'healthInfo' => $healthInfo,
        ];
        return response($response,201);
    }

    //test
    public static function getNextPatientAppointment(Request $request) {
        $patient = PatientController::getPatientByToken($request);
        $patient_id = $patient->patient_id;
        $now = Carbon::now()->addHour(3);
        $appointment = User::join('dentists','dentists.user_id','=','users.id')
        ->join('booked_appointments','booked_appointments.dentist_id','=','dentists.dentist_id')
        ->where('booked_appointments.patient_id',$patient_id)
        ->where('booked_appointments.appointment_date','>=',$now)
        ->where('booked_appointments.Done',False)
        ->orderBy('booked_appointments.appointment_date')
        ->get(['booked_appointments.appointment_id','booked_appointments.dentist_id','booked_appointments.appointment_date','booked_appointments.duration','booked_appointments.Done'])->first();
        if ($appointment == null) {
            $response = [
                'message' => 'this patient don\'t have any appointment'
            ];
            return response($response,401);
        }
        $dentist = Dentist::where('dentist_id',$appointment->dentist_id)->get(['user_id','city_id','location'])->first();
        $name = User::where('id',$dentist->user_id)->get('name')->first()->name;
        $city_name = City::where('city_id',$dentist->city_id)->get('city_name')->first()->city_name;
        
        $response = [
            'dentist_name' => $name,
            'city_name' => $city_name,
            'location' => $dentist->location,
            'appointment' => $appointment,
            'message' => 'success'
        ];
        return response($response,201);
    }

    public static function checkForAnotherAppointment(Request $request) {
        $patient_id = PatientController::getPatientByToken($request)->patient_id;
        $appointment = BookedAppointmentController::isExist($request,$patient_id);
        $result = $appointment->isEmpty();
        if ($result) return 0;
        return 1;
        
        
    }

    public static function addAppointment(Request $request) {
        $anchor = Carbon::today()->format('l');
        $date = Carbon::today();
        $dayofweek = date('w', strtotime($date));
        $day = $request->day;
        $add = $day - $dayofweek;
        //if ($day <$dayofweek) $add += 7;
        $appointment_date = date('Y-m-d', strtotime(($add).' day', strtotime($date))).' '.$request->time;
        $result = self::validateReq($request);
        //return $result;
        $patient_id = PatientController::getPatientByToken($request)->patient_id;
        //if (!self::isExist($request,$patient_id)) {
            if ((self::canBook($request,$appointment_date)) == 1 & self::checkForAnotherAppointment($request) == 0) {
                $bookedAppointment = new BookedAppointment;
                $bookedAppointment->dentist_id = $result['dentist_id'];
                $bookedAppointment->patient_id = $patient_id;
                $bookedAppointment->appointment_date = $appointment_date;
                $bookedAppointment->duration = $result['duration'];
                $bookedAppointment->save();
                $response = [
                    'bookedAppointment' => $bookedAppointment,
                    'done' => true,
                    'message' => 'تم الحجز'
                ];
                return response($response,201);
            }
            if (self::checkForAnotherAppointment($request) == 1) {
                $response = [
                    'done' => false,
                    'message' => 'لقد حجزت موعد موعد بالفعل لا يمكنك الحجز مرة أخرى'
                ];
                return response($response,401);

            }
            if (self::canBook($request,$appointment_date) == 0) {
                $response = [
                    'done' => false,
                    'message' => 'نعتذر لا يوجد مواعيد متاحة بهذا الوقت'
                ];
                return response($response,401);

            }
        //}
        $response = [
            'done' => false,
            'message' => 'لقد حجزت موعد موعد بالفعل لا يمكنك الحجز مرة أخرى'
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

    public static function checkBookedAppointment($result,$appointment_date) {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $appointment_date);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $appointment_date)
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
                return 0;
            }
        }
        return 1;
    }

    public static function canBook(Request $request,$appointment_date) {
        $result = MyValidator::make($request->only('dentist_id','day','duration'), [
            'dentist_id'=> 'required|exists:dentists,dentist_id',
            'day' => 'numeric',
            'duration' => 'required|numeric'
        ]);
        if (!$result['status']) {
            return 0;
        }
        $result = $result['data'];
        $patient_id = PatientController::getPatientByToken($request)->patient_id;
        $response = ScheduleController::checkWorkTime($result,$appointment_date,$patient_id);
        if ($response) {
            $response2 = BookedAppointmentController::checkBookedAppointment($result,$appointment_date);
            if ($response2) {
                return 1;
            }
            return $response2;
        }
        return $response;
    }
    
}
