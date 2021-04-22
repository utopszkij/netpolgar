<?php

// projekt feladatok

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
            $table->biginteger('project_id')->unsigned()->nullable();  // tuljdonos projekt
            $table->string('status')->nullabled(); // 'active' | 'inwork' | ....
            $table->string('type')->nullabled(); // 'bug' | 'question' .....
            $table->integer('prior')->nullabled(); // prioritás
            $table->integer('position')->nullabled(); // prioritás
            $table->string('txt')->nullabled();
            $table->biginteger('user_id')->unsigned();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->biginteger('created_by')->unsigned();
            //----
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
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
