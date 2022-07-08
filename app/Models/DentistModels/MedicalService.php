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
        'mservice_id',
        'service_name',
        'specialty_id'
    ];

    protected $primaryKey = 'mservice_id';
    
    public function Specialty(){
        return $this->belongsTo(Specialty::class,'specialty_id');
    }

    public function DentistService(){
        return $this->hasMany(DentistService::class,'mservice_id');
    }
}
