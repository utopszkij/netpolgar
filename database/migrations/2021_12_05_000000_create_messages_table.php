<?php

// üzenetek
//  több rekord tipushoz kapcsolódhat üzenet
//  üzenet tipusok: msg | pro | contra 

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
            $table->string('parent_type')->nullable(); 
            $table->biginteger('parent')->unsigned()->nullable();  
            $table->biginteger('reply_to')->unsigned()->nullable(); // beszégetás fa felépítése
            $table->biginteger('user_id')->unsigned()->nullable();  // üzenet küldő elehasználó
            $table->string('msg_type')->nullable();
            $table->mediumtext('value')->nullable(); // üzenet szöveg | érdemjegy
				$table->biginteger('moderated_by')->unsigned()->nullable();
            $table->mediumtext('moderator_info')->nullable(); // moderátor infó
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->index(['parent_type', 'parent']);
            $table->index(['user_id']);
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
