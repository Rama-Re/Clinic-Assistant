<?php

namespace App\Models\DentistModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SharedModels\BookedAppointment;
use App\Models\SharedModels\PatientRecord;
use App\Models\DentistModels\DentistService;
use App\Models\DentistModels\Schedule;
use App\Models\User;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'dentist_id',
        'user_id',
    ];

    protected $primaryKey = 'dentist_id';

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function BookedAppointment(){
        return $this->hasMany(BookedAppointment::class,'dentist_id');
    }

    public function PatientRecord(){
        return $this->hasMany(PatientRecord::class,'dentist_id');
    }

    public function DentistService(){
        return $this->hasMany(DentistService::class,'dentist_id');
    }
    
    public function Schedule(){
        return $this->hasMany(Schedule::class,'dentist_id');
    }
    
}
