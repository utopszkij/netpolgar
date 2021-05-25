<?php

// csoportok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->biginteger('parent_id')->unsigned()->nullable();  // fa szerkezet, tuljdonos
            $table->string('name')->nullable();  // megnevezés
            $table->mediumtext('description')->nullable(); // leírás
            $table->string('avatar')->nullable();  // avatar kép url
            $table->string('status')->nullable(); // 'suggestion' | 'active' | 'closed' | 'suspended'
            $table->mediumtext('config')->nullable(); // beállítások
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
        Schema::dropIfExists('groups');
    }
}
