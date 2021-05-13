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
            $table->string('parent_type')->nullable(); // groups | group_members
            											// projects | project_members | task
            											// products | transactions
            											// polls | options
            											// users
            $table->biginteger('parent_id')->unsigned()->nullable();  
            $table->biginteger('reply_to')->unsigned()->nullable(); // beszégetás fa felépítése
            $table->biginteger('user_id')->unsigned()->nullable();  // üzenet küldő elehasználó
            $table->string('type')->nullable();
            $table->mediumtext('value')->nullable(); // üzenet szöveg | érdemjegy
			$table->biginteger('moderated_by')->unsigned()->nullable();
            $table->mediumtext('moderatorinfo')->nullable(); // moderátor infó
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
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
