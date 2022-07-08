<?php

namespace App\Models\LocationModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LocationModels\City;
use App\Models\User;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        'location_id',
        'location_name',
        'city_id',
    ];

    protected $primaryKey = 'location_id';

    public function User(){
        return $this->hasMany(User::class,'location_id');
    }
    public function City(){
        return $this->belongsTo(City::class,'city_id');
    }
}
