<?php

namespace App\Models\DentistModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DentistModels\Dentist;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'schedule_id',
        'dentist_id',
        'working_day'
    ];

    protected $primaryKey = 'schedule_id';
    
    protected $time = ['start','end']; 

    public function Dentist(){
        return $this->belongsTo(Dentist::class,'dentist_id');
    }
}
