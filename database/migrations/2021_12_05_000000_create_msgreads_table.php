<?php

// üzenetek
//  több rekord tipushoz kapcsolódhat üzenet
//  üzenet tipusok: msg | pro | contra 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsgreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msgreads', function (Blueprint $table) {
            $table->id();
            $table->biginteger('msg_id')->unsigned()->nullable();  
            $table->biginteger('user_id')->unsigned()->nullable();  // üzenet küldő elehasználó
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('msgreads');
    }
}
