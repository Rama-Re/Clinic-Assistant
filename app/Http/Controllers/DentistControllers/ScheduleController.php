<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DentistModels\Schedule;
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
        //$schedule = Schedule::groupBy('working_day')->get();
        //Schedule::select(['working_day','start','end'])->groupBy('working_day')
        //->having('dentist_id', '=', $dentist_id)->orderBy('start')->orderBy('end')
        //->get();where('dentist_id',$dentist_id
        //groupBy('working_day')->where('dentist_id',)->get();
        return $scheduleArray;
    //->sortBy(function ($article) => constant('\Carbon\Carbon::' . strtoupper($article->day)))
    //->toBase();
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
}
