<?php

// szavazások

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->biginteger('group_id')->unsigned()->nullable();  // tuljdonos csoport
            $table->string('name')->nullabled();  // megnevezés
            $table->mediumtext('description')->nullabled();  // leírás
            $table->string('avatar')->nullabled();  // avatar kép url
            $table->string('status')->nullabled();  // státusz
            $table->string('type')->nullabled();  // szavazás tipusa
            $table->datetime('start')->nullabled();  // tényleges indulás
            $table->datetime('end')->nullabled();  // tényleges vége
            $table->mediumtext('config')->nullabled();  // konfiguráció
            $table->string('can_voks')->nullabled(); // ez a "ranks" szavazhat
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->biginteger('created_by')->unsigned();
            // ---
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls');
    }
}
