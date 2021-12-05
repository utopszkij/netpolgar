<?php

// csoport tagok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->string('parent_type'); // 'team'|'project'|'product'|'debate'
            $table->biginteger('parent')->unsigned()->nullable();  // tuljdonos csoport
            $table->biginteger('user_id')->unsigned()->nullable();  // felehasználó
            $table->string('like_type')->nullable();  // funció 'member' | 'admin' ...
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
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
        Schema::dropIfExists('likes');
    }
}
