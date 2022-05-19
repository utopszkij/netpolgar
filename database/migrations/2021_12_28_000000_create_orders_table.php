<?php

// csoportok

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_type');
            $table->biginteger('customer')->unsigned()->nullable();
            $table->string('status')->nullable(); // cart|order|ok|notok|success|canceled
            $table->mediumtext('description')->nullable();
            $table->mediumtext('address')->nullable();
            $table->mediumtext('shipping')->nullable();
            $table->mediumtext('confirmInfo')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->index(['customer_type','customer']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
