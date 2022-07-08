<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\LocationModels\Location;
use App\Models\PatientModels\Patient;
use App\Models\DentistModels\Dentist;
use App\Models\Admin;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'type',
        'phone_number',
        'location_id',
        'password',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'verification_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
    ];
    public function Location(){
        return $this->belongsTo(Location::class,'location_id');
    }
    public function Patient(){
        return $this->hasOne(Patient::class,'user_id');
    }
    public function Dentist(){
        return $this->hasOne(Dentist::class,'user_id');
    }
    public function Admin(){
        return $this->hasOne(Admin::class,'user_id');
    }
    
}
