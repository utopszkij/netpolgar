<?php

// üzenetek
//  több rekord tipushoz kapcsolódhat üzenet
//  ez atábla tárolja a like/dislájk -okat is
//  ez atábla tárolja az értékeléseket is
//  üzenet tipusok: msg | like | dislike | grade | support | oppose

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('parent_type')->nullabled(); // groups | group_members
            											// projects | project_members | task
            											// products | transactions
            											// polls | options
            											// users
            $table->biginteger('parent_id')->unsigned()->nullabled();  
            $table->biginteger('reply_to')->unsigned()->nullabled(); // beszégetás fa felépítése
            $table->biginteger('user_id')->unsigned()->nullabled();  // üzenet küldő elehasználó
            $table->string('type')->nullabled();
            $table->mediumtext('value')->nullabled(); // üzenet szöveg | érdemjegy
			$table->biginteger('moderated_by')->unsigned()->nullabled();
            $table->mediumtext('moderatorinfo')->nullabled(); // moderátor infó
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            //----
            $table->foreign('moderated_by')->references('id')->on('users');
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
        Schema::dropIfExists('messages');
    }
}
