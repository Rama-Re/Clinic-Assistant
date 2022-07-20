<?php

namespace App\Models\DentistModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DentistModels\MedicalService;

class Specialty extends Model
{
    use HasFactory;
    protected $fillable = [
        'specialty_id',
        'specialty_name'
    ];

    protected $primaryKey = 'specialty_id';
    
    public function SpecialtyService(){
        return $this->hasMany(SpecialtyService::class,'specialty_id');
    }

    public function DentistSpecialty(){
        return $this->hasMany(DentistSpecialty::class,'specialty_id');
    }
}
