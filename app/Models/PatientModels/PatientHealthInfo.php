<?php

namespace App\Models\PatientModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientModels\Patient;
use App\Models\PatientModels\Disease;
class PatientHealthInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'health_info_id',
        'patient_id',
        'disease_id',
    ];

    protected $primaryKey = 'health_info_id';

    public function Patient(){
        return $this->belongsTo(Patient::class,'patient_id');
    }
    public function Disease(){
        return $this->belongsTo(Disease::class,'disease_id');
    }
    
}
