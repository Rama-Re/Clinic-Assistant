<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booked_appointments', function (Blueprint $table) {
            $table->bigIncrements('appointment_id')->unique();
            $table->unsignedBigInteger('dentist_id');
            $table->foreign('dentist_id')->references('dentist_id')->on('dentists');
            $table->unsignedBigInteger('patient_id');   
            $table->foreign('patient_id')->references('patient_id')->on('patients');
            //$table->enum('day', ['Saturday', 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday']);
            $table->timestamp('appointment_date');
            $table->unsignedBigInteger('duration');
            $table->boolean('Done')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booked_appointments');
    }
};
