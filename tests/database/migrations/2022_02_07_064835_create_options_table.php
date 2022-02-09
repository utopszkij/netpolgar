<?php
/* eldöntendő kérdés,szavazás - választható opciók */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('poll_id')->comment("vita, szavazás");
            $table->string('name')->nullable()->comment("rövid megnevezés");
            $table->string('status')->nullable()->comment("proposal|active");
            $table->mediumText('description')->comment("leírás");
            $table->unsignedBigInteger('created_by')->comment("létrehozó user");
            $table->timestamp('created_at')->useCurrent()->comment("létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modositás időpontja");
            
            $table->foreign('poll_id', 'options_poll_id_foreign')->references('id')->on('polls');
            $table->foreign('created_by', 'options_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}
