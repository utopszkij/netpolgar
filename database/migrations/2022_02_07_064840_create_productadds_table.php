<?php
/* termék készlet növelések */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductaddsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productadds', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('product_id')->comment("produkt ID")->index('productadds_product_id_index');
            $table->double('quantity')->nullable()->comment("mennyiség");
            $table->unsignedBigInteger('user_id')->comment("létrehozó user");
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modositás időpontja");
            
            $table->foreign('user_id', 'productadds_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productadds');
    }
}
