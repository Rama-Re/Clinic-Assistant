<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DentistModels\Dentist;
use App\Http\Controllers\DentistControllers\DentistController;
use App\Http\Controllers\PatientControllers\PatientController;

class AuthController extends Controller
{
    public static function register(Request $request)
    {
        $result = $request->validate([
            'phone_number' => 'required|string|unique:users,phone_number',
            'password' => 'required|string|min:7|max:30',
            'name' => 'required|string',
            'type' => 'required|in:Dentist,Patient,Admin',
        ]);
        if ($result['type'] == 'Dentist') {
            $result2 = DentistController::validateReq($request);
            $user = new User;
            $user->phone_number = $result['phone_number'];
            $user->name = $result['name'];
            $user->type = $result['type'];
            $user->password = bcrypt($result['password']);
            $user->save();

            $dentist = DentistController::save($result2,$user->id);
            $response = [
                'user' => compact('user','dentist'),
                'message' => "Registered"
            ];
            return response($response,201);
        }
        else if ($result['type'] == 'Patient') {
            $result2 = PatientController::validateReq($request);
            $user = new User;
            $user->phone_number = $result['phone_number'];
            $user->name = $result['name'];
            $user->type = $result['type'];
            $user->password = bcrypt($result['password']);
            $user->save();

            $patient = PatientController::save($result2,$user->id);
            $response = [
                'user' => compact('user','patient'),
                'message' => "Registered"
            ];
            return response($response,201);
        }
    }

    public static function logout(Request $request) {
        $result = $request->user()->currentAccessToken()->delete();
        //$result = auth()->user()->tokens()->delete();

        $response = [
            'message' => 'Logged out',
            'result' => $result
        ];
        return response($response,201);
    }

    public static function login(Request $request)
    {
        $result = $request->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
            'password' => 'required|string',
        ]);

        $user = User::where('phone_number',$result['phone_number'])->first();
        
        if (!$user || !Hash::check($result['password'],$user->password)) {
            $response = [
                'message' => 'phone_number or password is not true',
            ];
            return response($response,401);
        }
        
        $token = $user->createToken('myapptoken')->plainTextToken;

        if ($user->type == 'Dentist') {
            $dentist = DentistController::get($user->id);
            $response = [
                'profile' => compact('user','dentist'),
                'token' => $token
            ];
            return response($response,201);
        }

        if ($user->type == 'Patient') {
            $patient = PatientController::get($user->id);
            $response = [
                'profile' => compact('user','patient'),
                'token' => $token
            ];
            return response($response,201);
        }
    }
}
