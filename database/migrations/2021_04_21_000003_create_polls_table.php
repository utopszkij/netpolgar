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
            $table->string('name')->nullable();  // megnevezés
            $table->mediumtext('description')->nullable();  // leírás
            $table->string('avatar')->nullable();  // avatar kép url
            $table->string('status')->nullable();  // státusz
            $table->string('type')->nullable();  // szavazás tipusa
            $table->datetime('start')->nullable();  // tényleges indulás
            $table->datetime('end')->nullable();  // tényleges vége
            $table->mediumtext('config')->nullable();  // konfiguráció
            $table->string('can_voks')->nullable(); // ez a "ranks" szavazhat
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->biginteger('created_by')->unsigned();
            // ---
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('created_by')->references('id')->on('users');
        }\n);
    }\n

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls');
    }\n
}\n
