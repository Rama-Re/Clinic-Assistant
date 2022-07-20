<?php

namespace App\Models\DentistModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SharedModels\BookedAppointment;
use App\Models\SharedModels\PatientRecord;
use App\Models\DentistModels\DentistService;
use App\Models\DentistModels\Schedule;
use App\Models\LocationModels\City;
use App\Models\User;

class Dentist extends Model
{
    use HasFactory;
    protected $fillable = [
        'dentist_id',
        'user_id',
        'location',
        'city_id',
        
    ];

    protected $date = 'work_starting_date';

    protected $primaryKey = 'dentist_id';

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function City(){
        return $this->belongsTo(City::class,'city_id');
    }

    public function BookedAppointment(){
        return $this->hasMany(BookedAppointment::class,'dentist_id');
    }

    public function PatientRecord(){
        return $this->hasMany(PatientRecord::class,'dentist_id');
    }
    
    public function Schedule(){
        return $this->hasMany(Schedule::class,'dentist_id');
    }
    
    public function DentistSpecialty(){
        return $this->hasMany(DentistSpecialty::class,'dentist_id');
    }
}
