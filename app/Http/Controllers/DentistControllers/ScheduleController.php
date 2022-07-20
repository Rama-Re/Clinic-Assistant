<?php

namespace App\Http\Controllers\DentistControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public static function validateReq(Request $request)
    {
        $result = $request->validate([
            'service_name' => 'required|string',
            'specialty_id' => 'required|exists:specialties,specialty_id',
        ]);
        return $result;
    }
}
