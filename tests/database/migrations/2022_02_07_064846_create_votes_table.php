<?php
/* leadott szavazatok */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->unsignedBigInteger('poll_id')->comment("szavazás ID");
            $table->unsignedBigInteger('ballot_id')->comment("szavazólap ID");
            $table->unsignedBigInteger('option_id')->comment("opció ID (erre szavaz)");
            $table->integer('position')->nullable()->comment("sorbarendező szavazásnál a pozió ahová sorolta");
            $table->bigInteger('accredited_id')->nullable()->comment("ha a user helyett a likvid képviselője szavazott");
            $table->bigInteger('user_id')->nullable()->comment("szavazó user ID (titkos szavazásnál nincs tárolva)");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("rekord utolsó modositás időpontja");
            
            $table->foreign('ballot_id', 'votes_ballot_id')->references('id')->on('ballots');
            $table->foreign('option_id', 'votes_option_id_foreign')->references('id')->on('options');
            $table->foreign('poll_id', 'votes_poll_id_foreign')->references('id')->on('polls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('votes');
    }
}
