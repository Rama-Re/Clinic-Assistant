<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Http\Controllers\DentistControllers\MedicalServiceController;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        MedicalServiceController::save();
    }
}
