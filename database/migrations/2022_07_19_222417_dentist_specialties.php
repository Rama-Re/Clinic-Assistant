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
        Schema::create('dentist_specialties', function (Blueprint $table) {
            $table->bigIncrements('dspecialty_id')->unique();
            $table->unsignedBigInteger('dentist_id');
            $table->foreign('dentist_id')->references('dentist_id')->on('dentists');
            $table->unsignedBigInteger('specialty_id');
            $table->foreign('specialty_id')->references('specialty_id')->on('specialties');
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
        Schema::dropIfExists('dentist_specialties');
    }
};
