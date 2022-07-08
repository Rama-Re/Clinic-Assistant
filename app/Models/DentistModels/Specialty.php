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
    
    public function MedicalService(){
        return $this->hasMany(MedicalService::class,'specialty_id');
    }
}
