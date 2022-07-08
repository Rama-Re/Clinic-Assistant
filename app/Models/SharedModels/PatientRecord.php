<?php

namespace App\Models\SharedModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientModels\Patient;
use App\Models\DentistModels\Dentist;
use App\Models\SharedModels\BookedAppointment;

class PatientRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'record_id',
        'appointment_id',
        'patient_id',
        'dentist_id',
        'notes'
    ];

    protected $primaryKey = 'record_id';

    public function Patient(){
        return $this->belongsTo(Patient::class,'patient_id');
    }
    public function Dentist(){
        return $this->belongsTo(Dentist::class,'dentist_id');
    }
    public function BookedAppointment(){
        return $this->belongsTo(BookedAppointment::class,'appointment_id');
    }

}
