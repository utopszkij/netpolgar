<?php
/* szavazó lapok. A szavazás megindulásakor generálódik minden szavazásra jogosult userhez. */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBallotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ballots', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('poll_id')->comment("vita/szavazás ID");
            $table->unsignedBigInteger('user_id')->comment("felhasználó")->index('user_id_index');
            
            $table->foreign('poll_id', 'ballots_poll_id_foreign')->references('id')->on('polls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ballots');
    }
}
