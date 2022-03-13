<?php

// csoportok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('parent_type');
            $table->biginteger('parent')->unsigned()->nullable();
            $table->string('name');
            $table->mediumtext('description')->unsigned()->nullable();
            $table->string('avatar')->nullable(); 
            $table->string('locatiob')->nullable(); 
            $table->date('date')->nullable();
            $table->integer('hours')->nullable(); 
            $table->integer('minutes')->nullable();
            $table->integer('kength')->nullable();
            $table->timestamp('createdd_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->biginteger('created_by')->unsigned()->nullable();
            $table->index(['created_by']);
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
