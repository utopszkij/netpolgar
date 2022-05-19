<?php

// taskok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->biginteger('project_id')->unsigned()->nullable(); 
            $table->mediumtext('name')->nullable(); // leírás
            $table->string('status')->nullable(); // waiting, active, inwork, canControl, closed
            $table->integer('position')->nullable(); // status -on belülii pozició 0,1,2..
				$table->date('deadline')->nullable();            
            $table->string('type')->nullable(); // bug, info, task, proposal
            $table->biginteger('assign')->unsigned()->nullable(); // user_id 
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
