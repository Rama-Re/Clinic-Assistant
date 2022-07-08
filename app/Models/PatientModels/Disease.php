<?php

namespace App\Models\PatientModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientModels\PatientHealthInfo;

class Disease extends Model
{
    use HasFactory;
    protected $fillable = [
        'disease_id',
        'disease_name'
    ];

    protected $primaryKey = 'disease_id';
    
    public function PatientHealthInfo(){
        return $this->hasMany(PatientHealthInfo::class,'disease_id');
    }
}
