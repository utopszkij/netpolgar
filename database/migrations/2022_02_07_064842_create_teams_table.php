<?php
/* csoportok */

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
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('parent')->nullable()->comment("csoport fa szerkezetet alakitja ki");
            $table->string('name')->nullable()->comment("csoport neve");
            $table->mediumText('description')->comment("csoport leírása");
            $table->string('avatar')->nullable()->comment("avatar kép url");
            $table->string('status')->nullable()->comment("proposal|active|closed");
            $table->mediumText('config')->comment("json string");
            $table->date('activated_at')->nullable()->comment("csoport megynitás dátuma");
            $table->date('closed_at')->nullable()->comment("csoport lezárás dátuma");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("rekord utolsó modisítás időpontja");
            $table->unsignedBigInteger('created_by')->comment("létrehozó user");
            
            $table->foreign('created_by', 'teams_created_by_foreign')->references('id')->on('users');
        });
/*
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
\DB::statement("
INSERT INTO `teams` (`id`,`parent`,`name`,`description`,`avatar`,
`status`,`config`,`activated_at`,`closed_at`,`created_at`,`updated_at`,
`created_by`)
VALUES
(2,0,'System admins',
'system admins',
'/img/team.png','active',
'{\"ranks\":[\"admin\",\"president\",\"manager\",\"moderator\"],\"close\":\"110\",\"memberActivate\":\"20\",\"memberExclude\":\"110\",\"rankActivate\":\"40\",\"rankClose\":\"95\",\"projectActivate\":\"2\",\"productActivate\":\"50\",\"subTeamActivate\":\"2\",\"debateActivate\":\"2\"}',
NULL,NULL,'2021-12-04 12:58:34','2021-12-04 18:21:41',7)
");
*/


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
