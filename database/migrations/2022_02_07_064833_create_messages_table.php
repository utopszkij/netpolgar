<?php
/* Üzenetek */

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
            $table->id()->comment("egyedi rekord ID");
            $table->string('parent_type')->nullable()->comment("teams|projects|tasks|polls|options|users|events|files");
            $table->unsignedBigInteger('parent')->comment("parent_type.id");
            $table->unsignedBigInteger('reply_to')->nullable()->comment("ha ez egy válasz, akkor erre válaszol (messages.id)");
            $table->unsignedBigInteger('user_id')->comment("üzenete küldő felhasználó");
            $table->string('msg_type')->nullable()->comment("későbbi fejlesztésre");
            $table->mediumText('value')->comment("üzenet szövege");
            $table->unsignedBigInteger('moderated_by')->nullable()->comment("moderátor user.id");
            $table->mediumText('moderator_info')->comment("moderátor megjegyzése");
            $table->timestamp('created_at')->useCurrent()->comment("létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modosítás időpontja");
            
            $table->index(['parent_type', 'parent'], 'messages_parent_type_parent_index');
            $table->foreign('user_id', 'messages_user_id_foreign')->references('id')->on('users');
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
