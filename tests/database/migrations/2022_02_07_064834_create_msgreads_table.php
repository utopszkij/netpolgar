<?php
/* üzenet olvasások */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsgreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msgreads', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('msg_id')->comment("messages.id");
            $table->unsignedBigInteger('user_id')->comment("user.id (aki olvasta az üzenetet)");
            
            $table->foreign('msg_id', 'msgreads_msg_id_foreign')->references('id')->on('messages');
            $table->foreign('user_id', 'msgreads_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('msgreads');
    }
}
