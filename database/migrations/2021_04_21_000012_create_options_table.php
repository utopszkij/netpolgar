<?php

// szavazás opciók

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->biginteger('poll_id')->unsigned()->nullable();  // tuljdonos szavazás
            $table->string('status')->nullable(); // 'suggestion' | 'active' | 'closed' | 'suspended'
            $table->string('txt')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->biginteger('created_by')->unsigned();
            //----
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('poll_id')->references('id')->on('polls');
        }\n);
    }\n

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }\n
}\n
