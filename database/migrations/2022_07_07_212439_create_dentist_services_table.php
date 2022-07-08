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
        Schema::create('dentist_services', function (Blueprint $table) {
            $table->bigIncrements('dservice_id')->unique();
            $table->unsignedBigInteger('mservice_id');
            $table->foreign('mservice_id')->references('mservice_id')->on('medical_services');
            $table->longText('notes');
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
        Schema::dropIfExists('dentist_services');
    }
};
