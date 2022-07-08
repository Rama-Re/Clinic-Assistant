<?php

namespace App\Models\DentistModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DentistModels\MedicalService;
use App\Models\DentistModels\Dentist;
class DentistService extends Model
{
    use HasFactory;
    protected $fillable = [
        'dservice_id',
        'mservice_id',
        'dentist_id',
        'notes'
    ];

    protected $primaryKey = 'dservice_id';
    
    public function MedicalService(){
        return $this->belongsTo(MedicalService::class,'mservice_id');
    }

    public function Dentist(){
        return $this->belongsTo(Dentist::class,'dentist_id');
    }
}
