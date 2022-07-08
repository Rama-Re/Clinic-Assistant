<?php

namespace App\Models\LocationModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LocationModels\Location;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'city_name',
    ];
    
    protected $primaryKey = 'city_id';

    public function Location(){
        return $this->hasMany(Location::class,'city_id');
    }
    
}
