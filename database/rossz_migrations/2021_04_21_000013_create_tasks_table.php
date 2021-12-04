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
            $table->string('status')->nullable(); // 'active' | 'inwork' | ....
            $table->string('type')->nullable(); // 'bug' | 'question' .....
            $table->integer('prior')->nullable(); // prioritás
            $table->integer('position')->nullable(); // prioritás
            $table->string('txt')->nullable();
            $table->biginteger('user_id')->unsigned();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->biginteger('created_by')->unsigned();
            //----
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        }\n);
    }\n

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }\n
}\n
