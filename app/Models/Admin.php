<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
    ];

    protected $primaryKey = 'admin_id';

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }

}
