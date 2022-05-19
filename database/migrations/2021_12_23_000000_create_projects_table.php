<?php

// csoportok

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
            $table->biginteger('team_id')->unsigned()->nullable(); 
            $table->string('name')->nullable();  // megnevezés
            $table->mediumtext('description')->nullable(); // leírás
            $table->string('avatar')->nullable();  // avatar kép url
            $table->string('status')->nullable(); // 'proposal' | 'active' | 'closed' | 'suspended'
				$table->date('deadline')->nullable();            
            $table->mediumtext('config')->nullable(); // beállítások json
				$table->date('activated_at')->nullable();            
				$table->date('closed_at')->nullable();            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->biginteger('created_by')->unsigned();
            //----
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
        Schema::dropIfExists('projects');
    }
}
