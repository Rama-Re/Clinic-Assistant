<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DentistModels\Schedule;
use Carbon\Carbon;


class ScheduleController extends Controller
{
    public static function validateReq(Request $request)
    {
        $result = $request->validate([
            'schedule' => 'required|array',
            'schedule.*.working_day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'schedule.*.times.*.start' => 'required',
            'schedule.*.times.*.end' => 'required',
        ]);
        return $result;
    }
    public static function save($result,$dentist_id)
    {
        $schedules = $result['schedule'];
        foreach ($schedules as $schedule) {
            $working_day = $schedule['working_day'];
            $times = $schedule['times'];
            foreach ($times as $time) {
                $schedule = new Schedule;
                $schedule->working_day = $working_day;
                $schedule->start = $time['start'];
                $schedule->end = $time['end'];
                $schedule->dentist_id = $dentist_id;
                $schedule->save();
                if (!$schedule) return False;
            }
        }
        return True;
    }

    public static function getSchedule($dentist_id)
    {
        $scheduleArray = array();
        $count = 0;
        $schedules = Schedule::select(['working_day','dentist_id'])
                ->groupBy('dentist_id','working_day')
                ->having('dentist_id', '=', $dentist_id)
                ->get();
        foreach ($schedules as $schedule) {
            $data = Schedule::where('dentist_id',$dentist_id)
                ->where('working_day',$schedule['working_day'])
                ->orderBy('start')
                ->get(['start','end']);
                $scheduleArray[$count] = [
                    'working_day' => $schedule['working_day'],
                    'times' => $data
                ];
                $count++;
        }
        
        return $scheduleArray;
    }

    public static function edit($result,$dentist_id)
    {
        if (Schedule::where('dentist_id',$dentist_id)->delete()) {
            $schedules = $result['schedule'];
            foreach ($schedules as $schedule) {
                $working_day = $schedule['working_day'];
                $times = $schedule['times'];
                foreach ($times as $time) {
                    $schedule = new Schedule;
                    $schedule->working_day = $working_day;
                    $schedule->start = $time['start'];
                    $schedule->end = $time['end'];
                    $schedule->dentist_id = $dentist_id;
                    $schedule->save();
                    if (!$schedule) return False;
                }
            }
            return True;
        }
        else return False;
    }
    public static function checkWorkTime($result,$appointment_date,$dentist_id){
        //return $result['appointment_date']->format('H:i:s');
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $appointment_date);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $appointment_date)
        ->addMinutes($result['duration']);
        $start2 = Carbon::createFromFormat('H:i:s', $start->format('H:i:s'));
        $end2 = Carbon::createFromFormat('H:i:s', $end->format('H:i:s'));
        //return $start2;
        $day = Carbon::createFromFormat('Y-m-d H:i:s', $appointment_date)->format('l');
        //return $end;
        //test
        $workTimes = Schedule::where('dentist_id',$result['dentist_id'])
        ->where('working_day',$day)->get(['start','end']);
        foreach ($workTimes as $time) {
            $startWork = Carbon::createFromFormat('H:i:s', $time['start']);
            //return $startWork;
            $endWork = Carbon::createFromFormat('H:i:s', $time['end']);
            if ($start2->gte($startWork) & $end2->lte($endWork)) {
                return 1;
            }
        }
        return 0;
    }
}
