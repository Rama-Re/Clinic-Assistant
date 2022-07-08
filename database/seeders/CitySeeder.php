<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Http\Controllers\LocationControllers\CityController;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        CityController::save();
    }
}
