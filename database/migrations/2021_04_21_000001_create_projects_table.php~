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
            $table->string('name')->nullabled();  // megnevezés
            $table->string('avatar')->nullabled();  // avatar kép url
            $table->mediumtext('description')->nullabled();  // leírás
            $table->string('status')->nullabled(); // 'suggestion' | 'active' | 'closed' | 'suspended' |  'canceled'
            $table->date('deadline')->nullabled(); // határidő
            $table->date('start')->nullabled();  // tényleges indulás
            $table->date('end')->nullabled();  // tényleges vége
            $table->mediumtext('config')->nullabled();  // konfiguráció
            // ---
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
