<?php

namespace App\Models\DentistModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DentistModels\MedicalService;
use App\Models\DentistModels\Specialty;

class SpecialtyService extends Model
{
    use HasFactory;
    protected $fillable = [
        'sservice_id',
        'specialty_id',
        'service_id'
        
    ];

    protected $primaryKey = 'sservice_id';

    public function Specialty(){
        return $this->belongsTo(Specialty::class,'specialty_id');
    }
    
    public function MedicalService(){
        return $this->belongsTo(MedicalService::class,'service_id');
    }
    

}
