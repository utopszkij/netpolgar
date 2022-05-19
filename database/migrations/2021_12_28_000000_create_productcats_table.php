<?php

// csoportok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductcatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productcats', function (Blueprint $table) {
            $table->id();
            $table->biginteger('product_id')->unsigned()->nullable();
            $table->string('category')->nullable();  // megnevezés
            $table->index(['product_id']);
            $table->index(['category']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productcats');
    }
}
