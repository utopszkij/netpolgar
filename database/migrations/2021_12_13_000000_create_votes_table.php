<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    /**
     * Leadott Szavazat rekord.
     * @return void
     * user szavazott megállapítása:
     * titkos szavazásnál:
     *   - nincs hozzá ballot rekord (szavazáskor ki lett törölve a user_id)
     * nyilt szavazásnál
     *   - van hozzá votes rekord   
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->biginteger('poll_id')->unsigned()->nullable();
            $table->biginteger('ballot_id')->nullable();
            $table->biginteger('option_id')->nullable();
            $table->integer('position')->nullable();
            $table->biginteger('accredited_id')->nullable(); // ha likvid és a képviselő
                                                             // szavazata lett lemásolva   
            $table->biginteger('user_id')->nullable(); // csak nyilt szavazásnál van kitöltve
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->index(['poll_id']);
            $table->index(['ballot_id']);
            
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
