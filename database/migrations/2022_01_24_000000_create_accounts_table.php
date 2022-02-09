<?php

// csoportok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('from_type');
            $table->biginteger('from')->unsigned()->nullable();
            $table->string('target_type');
            $table->biginteger('target')->unsigned()->nullable();
            $table->double('value')->nullable(); 
            $table->string('status')->nullable(); // ''|'locked'
            $table->string('comment')->nullable();
            $table->string('info')->nullable(); // pl.: 'oderItem:######'
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->index(['from_type','from']);
            $table->index(['target_type','target']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
