<?php

// csoportok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->biginteger('parent')->unsigned()->nullable();  // fa szerkezet, tuljdonos
            $table->string('name')->nullable();  // megnevezés
            $table->mediumtext('description')->nullable(); // leírás
            $table->string('avatar')->nullable();  // avatar kép url
            $table->string('status')->nullable(); // 'proposal' | 'active' | 'closed' | 'suspended'
            $table->mediumtext('config')->nullable(); // beállítások json
				$table->date('activated_at')->nullable();            
				$table->date('closed_at')->nullable();            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->biginteger('created_by')->unsigned();
            //----
            $table->foreign('created_by')->references('id')->on('users');
        });
		  \DB::statement("        
INSERT INTO `teams` (`id`,`parent`,`name`,`description`,`avatar`,
`status`,`config`,`activated_at`,`closed_at`,`created_at`,`updated_at`,
`created_by`) 
VALUES 
(1,0,'A regisztrált felhasználók',
'Minden regisztrált felhasználó automatikusan tagja ennek a csoportnak',
'/img/team.png','active',
'{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"110\",\"memberActivate\":\"0\",\"memberExclude\":\"110\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',
NULL,NULL,'2021-12-04 12:58:34','2021-12-04 18:21:41',7)
       ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
