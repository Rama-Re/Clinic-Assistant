<?php

namespace App\Models\LocationModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientModels\Patient;
use App\Models\DentistModels\Dentist;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'city_name',
    ];
    
    protected $primaryKey = 'city_id';

    public function Patient(){
        return $this->hasOne(Patient::class,'user_id');
    }
    public function Dentist(){
        return $this->hasOne(Dentist::class,'user_id');
    }
    
}
