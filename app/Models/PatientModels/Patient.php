<?php

namespace App\Models\PatientModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SharedModels\BookedAppointment;
use App\Models\SharedModels\PatientRecord;
use App\Models\PatientModels\PatientHealthInfo;
use App\Models\LocationModels\City;
use App\Models\User;
class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'location',
        'city_id'
    ];

    protected $primaryKey = 'patient_id';

    protected $date = 'bearth_date';

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function City(){
        return $this->belongsTo(City::class,'city_id');
    }

    public function BookedAppointment(){
        return $this->hasMany(BookedAppointment::class,'patient_id');
    }

    public function PatientRecord(){
        return $this->hasMany(PatientRecord::class,'patient_id');
    }
    public function PatientHealthInfo(){
        return $this->hasMany(PatientHealthInfo::class,'patient_id');
    }
}
