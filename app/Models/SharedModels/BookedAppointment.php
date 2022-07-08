<?php

namespace App\Models\SharedModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientModels\Patient;
use App\Models\DentistModels\Dentist;
use App\Models\SharedModels\PatientRecord;

class BookedAppointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'dentist_id',
        'day',
        'Done'
    ];

    protected $primaryKey = 'appointment_id';

    protected $date = 'appointment_date';

    protected $time = 'appointment_time';

    public function Patient(){
        return $this->belongsTo(Patient::class,'patient_id');
    }
    public function Dentist(){
        return $this->belongsTo(Dentist::class,'dentist_id');
    }
    public function PatientRecord(){
        return $this->hasOne(PatientRecord::class,'appointment_id');
    }
}
