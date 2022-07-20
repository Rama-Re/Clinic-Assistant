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
        Schema::create('specialty_services', function (Blueprint $table) {
            $table->bigIncrements('sservice_id')->unique();
            $table->unsignedBigInteger('specialty_id');
            $table->foreign('specialty_id')->references('specialty_id')->on('specialties');
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('service_id')->on('medical_services');
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
        Schema::dropIfExists('specialt_services');
    }
};
