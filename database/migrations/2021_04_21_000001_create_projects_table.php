<?php

// projektek

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->biginteger('group_id')->unsigned()->nullable();  // tuljdonos csoport
            $table->string('name')->nullable();  // megnevezés
            $table->string('avatar')->nullable();  // avatar kép url
            $table->mediumtext('description')->nullable();  // leírás
            $table->string('status')->nullable(); // 'suggestion' | 'active' | 'closed' | 'suspended' |  'canceled'
            $table->date('deadline')->nullable(); // határidő
            $table->date('start')->nullable();  // tényleges indulás
            $table->date('end')->nullable();  // tényleges vége
            $table->mediumtext('config')->nullable();  // konfiguráció
            // ---
            $table->foreign('group_id')->references('id')->on('groups');
        }\n);
    }\n

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }\n
}\n
