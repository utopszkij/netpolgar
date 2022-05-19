<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('parent_type')->nullable();
            $table->biginteger('parent')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable(); // proposal|debate|voks|closed|cancelled
            $table->date('debate_start')->nullable();
            $table->mediumtext('description')->nullable();
            $table->mediumtext('config')->nullable();
            $table->biginteger('created_by')->unsigned()->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->index(['parent_type', 'parent']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls');
    }
}
