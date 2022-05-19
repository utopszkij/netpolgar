<?php

// csoportok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('parent_type');
            $table->biginteger('parent')->unsigned()->nullable();
            $table->string('name');
            $table->mediumtext('description')->unsigned()->nullable();
            $table->string('type')->nullable(); 
            $table->string('licence')->nullable(); 
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
        Schema::dropIfExists('files');
    }
}
