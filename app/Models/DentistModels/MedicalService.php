<?php

namespace App\Models\DentistModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DentistModels\DentistService;
use App\Models\DentistModels\Specialty;

class MedicalService extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_id',
        'service_name',
    ];

    protected $primaryKey = 'service_id';
    
    public function SpecialtyService(){
        return $this->hasMany(SpecialtyService::class,'service_id');
    }
    
}
