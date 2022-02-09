<?php
/* kosár,megrendelés tételek */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderitems', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('order_id')->comment("megrendelés ID");
            $table->unsignedBigInteger('product_id')->comment("termék ID");
            $table->double('quantity')->nullable()->comment("mennyiség");
            $table->string('status')->nullable()->comment("open|ordering|confirmed|denied|closed1|closed2|canceled");
            $table->mediumText('confirmInfo')->comment("információ");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modositás időpontja");
            
            $table->foreign('order_id', 'orderitems_order_id_foreign')->references('id')->on('orders');
            $table->foreign('product_id', 'orderitems_product_id_foreign')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orderitems');
    }
}
