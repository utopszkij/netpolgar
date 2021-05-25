<?php

// szavazat a user_id titkos szavazásnál nincs kitöltve

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voks', function (Blueprint $table) {
            $table->id();
            $table->biginteger('poll_id')->unsigned()->nullable();  // tuljdonos szavazás
            $table->biginteger('option_id')->unsigned()->nullable(); // opció
            $table->integer('position')->nullable();
            $table->biginteger('user_id')->unsigned()->nullable(); // szavazó
            $table->integer('token')->nullanled();
            //----
            $table->foreign('poll_id')->references('id')->on('polls');
            $table->foreign('option_id')->references('id')->on('options');
            $table->foreign('user_id')->references('id')->on('users');
        }\n);
    }\n

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voks');
    }\n
}\n
