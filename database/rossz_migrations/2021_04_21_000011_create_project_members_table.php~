<?php

// projekt résztvevők
// egy user többször is szerepelhet (különböző "rank" adatokkal

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->biginteger('project_id')->unsigned()->nullable();  // tuljdonos project
            $table->biginteger('user_id')->unsigned()->nullable();  // felehasználó
            $table->string('rank')->nullable();  // funció 'member' | 'admin' ...
            $table->string('status')->nullable(); // 'suggestion' | 'active' | 'closed' | 'suspended'
			$table->date('activated_at')->nullable();            
			$table->date('closed_at')->nullable();            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->biginteger('created_by')->unsigned();
            //----
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_members');
    }
}
