<?php
/* bevásárló kosár vagy  megrendelés */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->string('customer_type')->nullable()->comment("megrendelő tipusa teams|users")->index('orders_user_id_index');
            $table->unsignedBigInteger('customer')->comment("megrendelő ID");
            $table->string('status')->nullable()->comment("open|ordering|closed2|closed2");
            $table->mediumText('description')->comment("leírás");
            $table->mediumText('address')->comment("szállítász, átvételi cím");
            $table->mediumText('shipping')->comment("szállítási mód");
            $table->mediumText('confirmInfo')->comment("információ");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("rekord modositás időpontja");
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
