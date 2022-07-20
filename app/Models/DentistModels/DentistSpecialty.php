<?php

namespace App\Models\DentistModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DentistModels\Specialty;
use App\Models\DentistModels\Dentist;


class DentistSpecialty extends Model
{
    use HasFactory;
    protected $fillable = [
        'dspecialty_id',
        'specialty_id',
        'dentist_id'
        
    ];

    protected $primaryKey = 'dspecialty_id';

    public function Specialty(){
        return $this->belongsTo(Specialty::class,'specialty_id');
    }
    
    public function Dentist(){
        return $this->belongsTo(Dentist::class,'dentist_id');
    }
}
